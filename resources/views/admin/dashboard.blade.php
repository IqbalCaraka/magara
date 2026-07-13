@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Overview</p>
                <h1 class="h3 mb-1">Dashboard</h1>
                <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->nama }}!</p>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <section class="row g-3 mt-1">
        <div class="col-6 col-md-3">
            <article class="metric-card metric-primary">
                <div class="metric-top">
                    <span class="metric-label">Total User</span>
                    <span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalUsers }}</div>
            </article>
        </div>
        <div class="col-6 col-md-3">
            <article class="metric-card metric-success">
                <div class="metric-top">
                    <span class="metric-label">Pencatatan Takah</span>
                    <span class="metric-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalTakah }}</div>
            </article>
        </div>
        <div class="col-6 col-md-3">
            <article class="metric-card metric-warning">
                <div class="metric-top">
                    <span class="metric-label">NIP Sudah Scan</span>
                    <span class="metric-icon"><i class="bi bi-upc-scan" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalScanning }}</div>
            </article>
        </div>
        <div class="col-6 col-md-3">
            <article class="metric-card metric-danger">
                <div class="metric-top">
                    <span class="metric-label">Daftar Isi Takah</span>
                    <span class="metric-icon"><i class="bi bi-folder2-open" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $totalDaftarIsi }}</div>
            </article>
        </div>
    </section>

    {{-- Grafik Aktivitas --}}
    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-graph-up" aria-hidden="true"></i><span>Grafik Aktivitas</span></h2>
                <p class="text-muted mb-0" id="chart-subtitle">7 hari terakhir</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary chart-period-btn" data-period="week">Minggu Ini</button>
                <button class="btn btn-sm btn-outline-secondary chart-period-btn" data-period="3month">3 Bulan</button>
            </div>
        </div>
        <div class="p-3">
            <canvas id="activityChart" height="100"></canvas>
        </div>
    </section>

    {{-- Statistik Per User --}}
    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-trophy" aria-hidden="true"></i><span>Statistik Per Pencatat</span></h2>
                <p class="text-muted mb-0" id="stats-subtitle">Semua waktu</p>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="form-label form-label-sm mb-0 small">Dari</label>
                    <input type="date" class="form-control form-control-sm" id="stats-from" style="max-width:160px">
                </div>
                <div>
                    <label class="form-label form-label-sm mb-0 small">Sampai</label>
                    <input type="date" class="form-control form-control-sm" id="stats-to" style="max-width:160px">
                </div>
                <button class="btn btn-sm btn-primary" id="btn-filter-stats">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="btn-reset-stats">Reset</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Role</th>
                        <th class="text-center">Pencatatan Takah</th>
                        <th class="text-center">Scanning (NIP)</th>
                        <th class="text-center">Scanning (Dok)</th>
                        <th class="text-center">Daftar Isi</th>
                    </tr>
                </thead>
                <tbody id="stats-tbody">
                    <tr><td colspan="7" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="px-3 pb-3 pt-2">
            <div class="d-flex gap-3 small" id="stats-summary">
                {{-- Filled by JS --}}
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ========== CHART ==========
    const ctx = document.getElementById('activityChart').getContext('2d');
    let chart = null;

    function loadChart(period) {
        fetch(`{{ route('admin.dashboard.chart') }}?period=${period}`)
            .then(r => r.json())
            .then(data => {
                if (chart) chart.destroy();

                const colors = [
                    { bg: 'rgba(13,110,253,0.15)', border: '#0d6efd' },
                    { bg: 'rgba(25,135,84,0.15)', border: '#198754' },
                    { bg: 'rgba(108,117,125,0.15)', border: '#6c757d' },
                ];

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: data.datasets.map((ds, i) => ({
                            label: ds.label,
                            data: ds.data,
                            borderColor: colors[i].border,
                            backgroundColor: colors[i].bg,
                            borderWidth: 2,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                        })),
                    },
                    options: {
                        responsive: true,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: {
                                callbacks: {
                                    label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y}`
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } },
                            x: { ticks: { maxRotation: 45 } },
                        },
                    },
                });

                document.getElementById('chart-subtitle').textContent =
                    period === '3month' ? '3 bulan terakhir' : '7 hari terakhir';
            });
    }

    document.querySelectorAll('.chart-period-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.chart-period-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');
            loadChart(this.dataset.period);
        });
    });

    loadChart('week');

    // ========== LEADERBOARD ==========
    function roleBadge(role) {
        const colors = { superadmin: 'danger', admin: 'primary', pic: 'warning', pkl: 'success', magang: 'info', viewer: 'secondary' };
        return `<span class="badge text-bg-${colors[role] || 'secondary'}">${role.charAt(0).toUpperCase() + role.slice(1)}</span>`;
    }

    function loadStats(from, to) {
        const params = new URLSearchParams();
        if (from) params.set('from', from);
        if (to) params.set('to', to);

        fetch(`{{ route('admin.dashboard.stats') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                const tbody = document.getElementById('stats-tbody');

                if (!res.leaderboard.length) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data dalam rentang ini.</td></tr>';
                    document.getElementById('stats-summary').innerHTML = '';
                    return;
                }

                let html = '';
                res.leaderboard.forEach((row, i) => {
                    html += `<tr>
                        <td>${i + 1}</td>
                        <td><strong>${row.nama}</strong></td>
                        <td>${roleBadge(row.role)}</td>
                        <td class="text-center"><span class="badge text-bg-primary">${row.takah_count}</span></td>
                        <td class="text-center"><span class="badge text-bg-success">${row.scan_nip_count}</span></td>
                        <td class="text-center"><span class="badge text-bg-info">${row.scan_doc_count}</span></td>
                        <td class="text-center"><span class="badge text-bg-secondary">${row.daftar_isi_count}</span></td>
                    </tr>`;
                });
                tbody.innerHTML = html;

                document.getElementById('stats-summary').innerHTML = `
                    <span class="text-muted">Total dalam rentang:</span>
                    <span><strong class="text-primary">${res.summary.takah}</strong> Pencatatan</span>
                    <span><strong class="text-success">${res.summary.scanning}</strong> Scanning</span>
                    <span><strong class="text-secondary">${res.summary.daftar_isi}</strong> Daftar Isi</span>
                `;

                const subtitle = from && to
                    ? `${formatDateId(from)} - ${formatDateId(to)}`
                    : from ? `Mulai ${formatDateId(from)}` : to ? `Sampai ${formatDateId(to)}` : 'Semua waktu';
                document.getElementById('stats-subtitle').textContent = subtitle;
            })
            .catch(() => {
                document.getElementById('stats-tbody').innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            });
    }

    function formatDateId(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    document.getElementById('btn-filter-stats').addEventListener('click', function () {
        loadStats(
            document.getElementById('stats-from').value,
            document.getElementById('stats-to').value
        );
    });

    document.getElementById('btn-reset-stats').addEventListener('click', function () {
        document.getElementById('stats-from').value = '';
        document.getElementById('stats-to').value = '';
        loadStats();
    });

    loadStats();
});
</script>
@endpush
