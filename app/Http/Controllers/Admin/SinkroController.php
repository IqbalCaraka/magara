<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return view('admin.sinkro.index', compact('instansiList'));
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

        if ($kategori = $request->input('kategori')) {
            $query->where('kategori_kelengkapan_2026', $kategori);
        }

        $data = $query->orderBy('nama')->paginate(15);

        return response()->json($data);
    }

    // --- Sinkro Actions ---

    public function sinkroInstansi()
    {
        try {
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
