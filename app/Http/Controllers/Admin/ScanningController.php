<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsipPns;
use App\Models\DokumenScanning;
use App\Models\PencatatanTakah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanningController extends Controller
{
    public function show(string $nip)
    {
        $asn = ArsipPns::with('instansi')->where('nip', $nip)->firstOrFail();
        $takah = PencatatanTakah::where('nip', $nip)->first();
        $dokumens = DokumenScanning::where('nip', $nip)
            ->with('creator')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.scanning.show', compact('asn', 'takah', 'dokumens', 'nip'));
    }

    public function store(Request $request, string $nip)
    {
        $validated = $request->validate([
            'jenis_dokumen' => 'required|array|min:1',
            'jenis_dokumen.*' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $nip) {
            $now = now();
            $records = [];
            foreach ($validated['jenis_dokumen'] as $jenis) {
                $records[] = [
                    'nip' => $nip,
                    'jenis_dokumen' => $jenis,
                    'created_by' => auth()->id(),
                    'date_created' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DokumenScanning::insert($records);
            $this->syncScanCount($nip);
        });

        return redirect()->route('admin.scanning.show', $nip)
            ->with('success', count($validated['jenis_dokumen']) . ' dokumen scanning berhasil dicatat.');
    }

    public function destroy(DokumenScanning $scanning)
    {
        $nip = $scanning->nip;

        DB::transaction(function () use ($scanning, $nip) {
            $scanning->delete();
            $this->syncScanCount($nip);
        });

        return redirect()->route('admin.scanning.show', $nip)
            ->with('success', 'Dokumen scanning berhasil dihapus.');
    }

    private function syncScanCount(string $nip): void
    {
        // Atomic update: hitung langsung dalam 1 query, tidak ada celah race condition
        PencatatanTakah::where('nip', $nip)->update([
            'scan_count' => DokumenScanning::where('nip', $nip)->count(),
        ]);
    }
}
