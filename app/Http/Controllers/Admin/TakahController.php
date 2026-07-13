<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArsipPns;
use App\Models\Instansi;
use App\Models\PencatatanTakah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TakahController extends Controller
{
    public function index()
    {
        $instansiList = Instansi::orderBy('nama')->get();

        // Summary counts - role pkl/magang hanya lihat data sendiri
        $baseQuery = PencatatanTakah::query();
        if (in_array(auth()->user()->role, ['pkl', 'magang'])) {
            $baseQuery->where('created_by', auth()->id());
        }
        $totalTakah = (clone $baseQuery)->count();
        $nipsTakahOnly = (clone $baseQuery)->where('scan_count', 0)->count();
        $nipsWithScanning = (clone $baseQuery)->where('scan_count', '>', 0)->count();
        $nipsWithDaftarIsi = 0; // Daftar isi takah belum ada

        return view('admin.takah.index', compact(
            'instansiList', 'totalTakah', 'nipsWithScanning', 'nipsWithDaftarIsi', 'nipsTakahOnly'
        ));
    }

    public function create()
    {
        $instansiList = Instansi::orderBy('nama')->get();

        return view('admin.takah.create', compact('instansiList'));
    }

    public function cariAsn(Request $request)
    {
        $query = ArsipPns::with('instansi');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        if ($instansiId = $request->input('instansi_id')) {
            $query->where('instansi_kerja_id', $instansiId);
        }

        $data = $query->orderBy('nama')->paginate(10);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string',
            'kode_rak' => 'required|string',
            'posisi_takah' => 'required|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['date_created'] = now();

        PencatatanTakah::create($validated);

        return redirect()->route('admin.takah.index')->with('success', 'Pencatatan takah berhasil ditambahkan.');
    }

    public function edit(PencatatanTakah $takah)
    {
        $asn = ArsipPns::with('instansi')->where('nip', $takah->nip)->first();

        return view('admin.takah.edit', compact('takah', 'asn'));
    }

    public function update(Request $request, PencatatanTakah $takah)
    {
        $validated = $request->validate([
            'nip' => 'required|string',
            'kode_rak' => 'required|string',
            'posisi_takah' => 'required|string',
            'status' => 'boolean',
        ]);

        $takah->update($validated);

        return redirect()->route('admin.takah.index')->with('success', 'Pencatatan takah berhasil diperbarui.');
    }

    public function destroy(PencatatanTakah $takah)
    {
        $takah->delete();

        return redirect()->route('admin.takah.index')->with('success', 'Data takah berhasil dihapus.');
    }

    public function data(Request $request)
    {
        // Subquery untuk ambil 1 arsip_pns per nip (hindari duplikat dari JOIN)
        $query = PencatatanTakah::with('creator')
            ->leftJoin('arsip_pns', function ($join) {
                $join->on('pencatatan_takah.nip', '=', 'arsip_pns.nip')
                     ->whereRaw('arsip_pns.id = (SELECT MIN(ap2.id) FROM arsip_pns ap2 WHERE ap2.nip = pencatatan_takah.nip)');
            })
            ->leftJoin('instansi', 'arsip_pns.instansi_kerja_id', '=', 'instansi.id')
            ->select(
                'pencatatan_takah.*',
                'arsip_pns.nama as asn_nama',
                'instansi.nama as instansi_nama'
            );

        // Role pkl/magang hanya lihat data sendiri
        if (in_array(auth()->user()->role, ['pkl', 'magang'])) {
            $query->where('pencatatan_takah.created_by', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pencatatan_takah.nip', 'like', "%{$search}%")
                  ->orWhere('pencatatan_takah.kode_rak', 'like', "%{$search}%")
                  ->orWhere('pencatatan_takah.posisi_takah', 'like', "%{$search}%")
                  ->orWhere('arsip_pns.nama', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '') {
            $query->where('pencatatan_takah.status', $request->input('status'));
        }

        if ($instansiIds = $request->input('instansi_ids')) {
            $ids = is_array($instansiIds) ? $instansiIds : explode(',', $instansiIds);
            $query->whereIn('arsip_pns.instansi_kerja_id', $ids);
        }

        // Filter by flow progress - langsung dari kolom scan_count
        if ($step = $request->input('step')) {
            if ($step === 'takah_only') {
                $query->where('pencatatan_takah.scan_count', 0);
            } elseif ($step === 'scanned') {
                $query->where('pencatatan_takah.scan_count', '>', 0);
            }
        }

        $data = $query->orderByDesc('pencatatan_takah.created_at')->paginate(15);

        return response()->json($data);
    }

    public function exportExcel(Request $request)
    {
        $query = PencatatanTakah::select('pencatatan_takah.*');

        // Filter: hanya belum generate
        if ($request->input('only_belum_generate')) {
            $query->where('pencatatan_takah.status', false);
        }

        // Filter: instansi tertentu - subquery untuk hindari duplikat
        if ($instansiIds = $request->input('instansi_ids')) {
            $ids = is_array($instansiIds) ? $instansiIds : explode(',', $instansiIds);
            $nips = ArsipPns::whereIn('instansi_kerja_id', $ids)->distinct()->pluck('nip');
            $query->whereIn('pencatatan_takah.nip', $nips);
        }

        $records = $query->orderBy('pencatatan_takah.nip')->get();

        if ($records->isEmpty()) {
            return response()->json(['error' => 'Tidak ada data untuk di-export.'], 422);
        }

        // Build spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pencatatan Takah');

        // Header - nama kolom lowercase agar sesuai format import DMS
        $headers = ['nip', 'kode_rak', 'posisi_takah'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
        }

        // Style header
        $headerRange = 'A1:C1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Data rows
        $row = 2;
        foreach ($records as $record) {
            $sheet->setCellValueExplicit("A{$row}", $record->nip, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("B{$row}", $record->kode_rak);
            $sheet->setCellValue("C{$row}", $record->posisi_takah);

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);

            $row++;
        }

        // Auto width
        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Download dulu, baru mark status setelah file berhasil dibuat
        $filename = 'pencatatan_takah_' . date('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $temp = tempnam(sys_get_temp_dir(), 'takah_');
        $writer->save($temp);

        // Mark exported records as status = 1 (setelah file berhasil dibuat)
        $exportedIds = $records->pluck('id')->toArray();
        PencatatanTakah::whereIn('id', $exportedIds)->update(['status' => true]);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
