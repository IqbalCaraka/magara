<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\ArsipPns;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SinkroController extends Controller
{
    public function index()
    {
        $instansiList = Instansi::orderBy('nama')->get();
        $dmsConfig = AppSetting::getDmsConfig();
        $dmsConfigured = !empty($dmsConfig['host']) && !empty($dmsConfig['database']);

        return view('admin.sinkro.index', compact('instansiList', 'dmsConfigured'));
    }

    /**
     * Set DMS connection config dari database (encrypted).
     */
    private function configureDms(): void
    {
        $config = AppSetting::getDmsConfig();

        config([
            'database.connections.dms.host' => $config['host'],
            'database.connections.dms.port' => $config['port'],
            'database.connections.dms.database' => $config['database'],
            'database.connections.dms.username' => $config['username'],
            'database.connections.dms.password' => $config['password'],
        ]);

        // Purge existing connection agar config baru dipakai
        DB::purge('dms');
    }

    public function settingDms()
    {
        $config = AppSetting::getDmsConfig();

        return view('admin.sinkro.setting', compact('config'));
    }

    public function updateSettingDms(Request $request)
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|string',
            'database' => 'required|string',
            'username' => 'required|string',
            'password' => 'nullable|string',
        ]);

        AppSetting::setValue('dms_db_host', $request->input('host'));
        AppSetting::setValue('dms_db_port', $request->input('port'));
        AppSetting::setValue('dms_db_database', $request->input('database'));
        AppSetting::setValue('dms_db_username', $request->input('username'));

        // Password hanya diupdate jika diisi (agar tidak overwrite saat edit lain)
        if ($request->filled('password')) {
            AppSetting::setValue('dms_db_password', $request->input('password'), encrypt: true);
        }

        return redirect()->route('admin.sinkro.index')->with('success', 'Konfigurasi DMS berhasil disimpan.');
    }

    public function testConnection()
    {
        try {
            $this->configureDms();
            DB::connection('dms')->getPdo();

            return response()->json(['success' => true, 'message' => 'Koneksi ke DMS berhasil!']);
        } catch (\Exception $e) {
            Log::error('Test koneksi DMS gagal: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Gagal koneksi ke DMS. Periksa konfigurasi.'], 422);
        }
    }

    // --- AJAX Pagination Endpoints ---

    public function instansiData(Request $request)
    {
        $query = Instansi::query();

        if ($search = $request->input('search')) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $data = $query->orderBy('nama')->paginate(15);

        return response()->json($data);
    }

    public function arsipPnsData(Request $request)
    {
        $query = ArsipPns::with('instansi');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        if ($instansiId = $request->input('instansi_id')) {
            $query->where('instansi_kerja_id', $instansiId);
        }

        if ($request->has('is_kedhuk_aktif') && $request->input('is_kedhuk_aktif') !== '') {
            $query->where('is_kedhuk_aktif', $request->input('is_kedhuk_aktif'));
        }

        $data = $query->orderBy('nama')->paginate(15);

        return response()->json($data);
    }

    // --- Sinkro Actions ---

    public function sinkroInstansi()
    {
        try {
            $this->configureDms();
            $dataRemote = DB::connection('dms')
                ->table('instansi')
                ->where('kantor_regional_id', '00')
                ->where('jenis', 'P')
                ->where('status', 'A')
                ->select('id', 'nama')
                ->get();

            if ($dataRemote->isEmpty()) {
                return back()->with('error', 'Tidak ada data instansi dari DMS yang sesuai filter.');
            }

            $synced = DB::transaction(function () use ($dataRemote) {
                $upsertData = $dataRemote->map(fn($row) => [
                    'id' => $row->id,
                    'nama' => $row->nama,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->toArray();

                Instansi::upsert($upsertData, ['id'], ['nama', 'updated_at']);

                return count($upsertData);
            });

            return back()->with('success', "Berhasil sinkronisasi {$synced} data instansi dari DMS.");
        } catch (\Exception $e) {
            Log::error('Sinkro Instansi gagal: ' . $e->getMessage());

            return back()->with('error', 'Gagal koneksi ke DMS. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function sinkroArsipPns(Request $request)
    {
        $instansiId = $request->input('instansi_id');

        try {
            $this->configureDms();
            $instansiIds = Instansi::pluck('id')->toArray();

            if (empty($instansiIds)) {
                return back()->with('error', 'Sinkro instansi terlebih dahulu sebelum sinkro arsip PNS.');
            }

            $synced = 0;

            DB::transaction(function () use ($instansiId, $instansiIds, &$synced) {
                $query = DB::connection('dms')
                    ->table('arsip_pns')
                    ->select('id', 'nip', 'nama', 'instansi_kerja_id', 'is_kedhuk_aktif', 'status_arsip', 'kategori_kelengkapan_2026', 'skor_arsip_2026');

                if ($instansiId) {
                    $query->where('instansi_kerja_id', $instansiId);
                } else {
                    $query->whereIn('instansi_kerja_id', $instansiIds);
                }

                $query->orderBy('id')->chunk(1000, function ($rows) use (&$synced) {
                    $upsertData = [];
                    $now = now();

                    foreach ($rows as $row) {
                        $upsertData[] = [
                            'id' => $row->id,
                            'nip' => $row->nip,
                            'nama' => $row->nama,
                            'instansi_kerja_id' => $row->instansi_kerja_id,
                            'is_kedhuk_aktif' => $row->is_kedhuk_aktif,
                            'status_arsip' => $row->status_arsip,
                            'kategori_kelengkapan_2026' => $row->kategori_kelengkapan_2026,
                            'skor_arsip_2026' => $row->skor_arsip_2026,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }

                    ArsipPns::upsert($upsertData, ['id'], [
                        'nip', 'nama', 'instansi_kerja_id', 'is_kedhuk_aktif',
                        'status_arsip', 'kategori_kelengkapan_2026', 'skor_arsip_2026',
                        'updated_at',
                    ]);

                    $synced += count($rows);
                });
            });

            $label = $instansiId
                ? Instansi::find($instansiId)?->nama ?? 'instansi terpilih'
                : 'semua instansi pusat';

            return back()->with('success', "Berhasil sinkronisasi {$synced} arsip PNS ({$label}).");
        } catch (\Exception $e) {
            Log::error('Sinkro Arsip PNS gagal: ' . $e->getMessage());

            return back()->with('error', 'Gagal sinkro arsip PNS. Silakan coba lagi atau hubungi administrator.');
        }
    }
}
