<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DokumenScanning;
use App\Models\PencatatanTakah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalTakah' => PencatatanTakah::count(),
            'totalScanning' => DokumenScanning::distinct('nip')->count('nip'),
            'totalDaftarIsi' => 0,
        ]);
    }

    public function stats(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');

        // Leaderboard: per user, jumlah takah, scanning, daftar isi
        $takahPerUser = PencatatanTakah::join('users', 'pencatatan_takah.created_by', '=', 'users.id')
            ->select('users.id', 'users.nama', 'users.role', DB::raw('COUNT(*) as takah_count'))
            ->when($from, fn($q) => $q->whereDate('pencatatan_takah.date_created', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('pencatatan_takah.date_created', '<=', $to))
            ->groupBy('users.id', 'users.nama', 'users.role')
            ->orderByDesc('takah_count')
            ->get();

        $scanPerUser = DokumenScanning::join('users', 'dokumen_scanning.created_by', '=', 'users.id')
            ->select('users.id', DB::raw('COUNT(DISTINCT dokumen_scanning.nip) as scan_nip_count'), DB::raw('COUNT(*) as scan_doc_count'))
            ->when($from, fn($q) => $q->whereDate('dokumen_scanning.date_created', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('dokumen_scanning.date_created', '<=', $to))
            ->groupBy('users.id')
            ->get()
            ->keyBy('id');

        $leaderboard = $takahPerUser->map(function ($user) use ($scanPerUser) {
            $scan = $scanPerUser->get($user->id);
            return [
                'nama' => $user->nama,
                'role' => $user->role,
                'takah_count' => $user->takah_count,
                'scan_nip_count' => $scan?->scan_nip_count ?? 0,
                'scan_doc_count' => $scan?->scan_doc_count ?? 0,
                'daftar_isi_count' => 0,
            ];
        });

        // Summary in range
        $totalTakahRange = PencatatanTakah::query()
            ->when($from, fn($q) => $q->whereDate('date_created', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date_created', '<=', $to))
            ->count();

        $totalScanRange = DokumenScanning::query()
            ->when($from, fn($q) => $q->whereDate('date_created', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date_created', '<=', $to))
            ->count();

        return response()->json([
            'leaderboard' => $leaderboard->values(),
            'summary' => [
                'takah' => $totalTakahRange,
                'scanning' => $totalScanRange,
                'daftar_isi' => 0,
            ],
        ]);
    }

    public function chart(Request $request)
    {
        $period = $request->input('period', 'week');
        $now = now();

        if ($period === '3month') {
            $from = $now->copy()->subMonths(3)->startOfDay();
        } else {
            $from = $now->copy()->subDays(6)->startOfDay();
        }

        $takahChart = PencatatanTakah::select(
                DB::raw("DATE(date_created) as tanggal"),
                DB::raw('COUNT(*) as jumlah')
            )
            ->where('date_created', '>=', $from)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('jumlah', 'tanggal');

        $scanChart = DokumenScanning::select(
                DB::raw("DATE(date_created) as tanggal"),
                DB::raw('COUNT(DISTINCT nip) as jumlah')
            )
            ->where('date_created', '>=', $from)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('jumlah', 'tanggal');

        // Build date labels
        $labels = [];
        $d = $from->copy();
        while ($d->lte($now)) {
            $labels[] = $d->format('Y-m-d');
            $d->addDay();
        }

        $takahData = array_map(fn($l) => $takahChart[$l] ?? 0, $labels);
        $scanData = array_map(fn($l) => $scanChart[$l] ?? 0, $labels);
        $daftarIsiData = array_fill(0, count($labels), 0);

        // Format labels for display
        $displayLabels = array_map(function ($l) {
            return date('d M', strtotime($l));
        }, $labels);

        return response()->json([
            'labels' => $displayLabels,
            'datasets' => [
                ['label' => 'Pencatatan Takah', 'data' => $takahData],
                ['label' => 'Scanning', 'data' => $scanData],
                ['label' => 'Daftar Isi', 'data' => $daftarIsiData],
            ],
        ]);
    }
}
