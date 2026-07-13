@extends('layouts.admin')

@section('title', 'Sinkronisasi Data DMS')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-arrow-repeat" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Sinkronisasi</p>
                <h1 class="h3 mb-1">Data DMS</h1>
                <p class="text-muted mb-0">Sinkronisasi data dari database DMS ke lokal.</p>
            </div>
        </div>
    </div>

    {{-- Tombol Sinkro --}}
    <section class="row g-3 mt-1">
        {{-- Sinkro Instansi --}}
        <div class="col-12 col-md-4">
            <div class="panel h-100">
                <div class="panel-header">
                    <h2 class="h5 mb-0 section-title"><i class="bi bi-building" aria-hidden="true"></i><span>Instansi</span></h2>
                </div>
                <div class="p-3">
                    <p class="text-muted mb-2">Kanreg 00, Jenis P, Status A</p>
                    <form action="{{ route('admin.sinkro.instansi') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="bi bi-arrow-repeat"></i> Sinkro Instansi
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sinkro Arsip PNS --}}
        <div class="col-12 col-md-8">
            <div class="panel h-100">
                <div class="panel-header">
                    <h2 class="h5 mb-0 section-title"><i class="bi bi-archive" aria-hidden="true"></i><span>Arsip PNS</span></h2>
                </div>
                <div class="p-3">
                    <p class="text-muted mb-2">PNS dari kanreg 00 / instansi pusat</p>
                    <form action="{{ route('admin.sinkro.arsip-pns') }}" method="POST" class="d-flex flex-wrap gap-2 align-items-end">
                        @csrf
                        <div class="flex-grow-1" style="min-width: 200px;">
                            <label class="form-label form-label-sm mb-1" for="instansi_id">Pilih Instansi</label>
                            <select class="form-select form-select-sm" name="instansi_id" id="instansi_id">
                                <option value="">-- Semua Instansi --</option>
                                @foreach($instansiList as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="bi bi-arrow-repeat"></i> Sinkro Arsip PNS
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Tabel Instansi --}}
    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-building" aria-hidden="true"></i><span>Data Instansi</span></h2>
                <p class="text-muted mb-0" id="instansi-info">Memuat data...</p>
            </div>
            <input class="form-control form-control-sm table-search" style="max-width:250px" type="search" placeholder="Cari instansi..." id="search-instansi">
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID</th>
                        <th>Nama Instansi</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody id="instansi-tbody">
                    <tr><td colspan="4" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mt-3 px-3 pb-3">
            <p class="text-muted small mb-0" id="instansi-showing"></p>
            <nav id="instansi-pagination"></nav>
        </div>
    </section>

    {{-- Tabel Arsip PNS --}}
    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-archive" aria-hidden="true"></i><span>Data Arsip PNS</span></h2>
                <p class="text-muted mb-0" id="arsip-info">Memuat data...</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <select class="form-select form-select-sm" style="max-width:220px" id="filter-instansi-arsip">
                    <option value="">Semua Instansi</option>
                    @foreach($instansiList as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->nama }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm" style="max-width:160px" id="filter-kategori">
                    <option value="">Semua Kategori</option>
                    <option value="Lengkap">Lengkap</option>
                    <option value="Tidak Lengkap">Tidak Lengkap</option>
                    <option value="Belum Diverifikasi">Belum Diverifikasi</option>
                </select>
                <input class="form-control form-control-sm" style="max-width:200px" type="search" placeholder="Cari NIP/Nama..." id="search-arsip">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Instansi</th>
                        <th>Kategori 2026</th>
                        <th>Skor 2026</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody id="arsip-tbody">
                    <tr><td colspan="7" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mt-3 px-3 pb-3">
            <p class="text-muted small mb-0" id="arsip-showing"></p>
            <nav id="arsip-pagination"></nav>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- Helper: Build pagination ---
    function buildPagination(meta, containerId, callback) {
        const nav = document.getElementById(containerId);
        if (meta.last_page <= 1) { nav.innerHTML = ''; return; }

        let html = '<ul class="pagination pagination-sm mb-0">';
        // Prev
        html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${meta.current_page - 1}">Previous</a></li>`;

        // Pages - show max 7 pages with ellipsis
        let start = Math.max(1, meta.current_page - 3);
        let end = Math.min(meta.last_page, meta.current_page + 3);

        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        if (end < meta.last_page) {
            if (end < meta.last_page - 1) html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${meta.last_page}">${meta.last_page}</a></li>`;
        }

        // Next
        html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${meta.current_page + 1}">Next</a></li>`;
        html += '</ul>';

        nav.innerHTML = html;
        nav.querySelectorAll('a[data-page]').forEach(a => {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                if (!this.closest('.page-item').classList.contains('disabled') &&
                    !this.closest('.page-item').classList.contains('active')) {
                    callback(parseInt(this.dataset.page));
                }
            });
        });
    }

    function showingText(meta) {
        if (meta.total === 0) return 'Tidak ada data.';
        return `Menampilkan ${meta.from}-${meta.to} dari ${meta.total} data`;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function kategoriBadge(kategori) {
        if (!kategori) return '<span class="badge text-bg-secondary">-</span>';
        const colors = { 'Lengkap': 'success', 'Tidak Lengkap': 'danger', 'Belum Diverifikasi': 'warning' };
        return `<span class="badge text-bg-${colors[kategori] || 'secondary'}">${kategori}</span>`;
    }

    // --- INSTANSI TABLE ---
    let instansiPage = 1;
    let instansiSearch = '';
    let instansiTimer;

    function loadInstansi(page) {
        instansiPage = page || 1;
        const params = new URLSearchParams({ page: instansiPage });
        if (instansiSearch) params.set('search', instansiSearch);

        fetch(`{{ route('admin.sinkro.instansi.data') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                const tbody = document.getElementById('instansi-tbody');
                const meta = { current_page: res.current_page, last_page: res.last_page, total: res.total, from: res.from, to: res.to };

                document.getElementById('instansi-info').textContent = `Total: ${res.total} instansi`;
                document.getElementById('instansi-showing').textContent = showingText(meta);

                if (!res.data.length) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada data instansi.</td></tr>';
                    document.getElementById('instansi-pagination').innerHTML = '';
                    return;
                }

                let html = '';
                res.data.forEach((row, i) => {
                    const no = (res.current_page - 1) * res.per_page + i + 1;
                    html += `<tr>
                        <td>${no}</td>
                        <td><code>${row.id.substring(0, 12)}...</code></td>
                        <td>${row.nama}</td>
                        <td>${formatDate(row.updated_at)}</td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                buildPagination(meta, 'instansi-pagination', loadInstansi);
            })
            .catch(() => {
                document.getElementById('instansi-tbody').innerHTML =
                    '<tr><td colspan="4" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            });
    }

    document.getElementById('search-instansi').addEventListener('input', function () {
        clearTimeout(instansiTimer);
        instansiTimer = setTimeout(() => { instansiSearch = this.value; loadInstansi(1); }, 400);
    });

    loadInstansi(1);

    // --- ARSIP PNS TABLE ---
    let arsipPage = 1;
    let arsipSearch = '';
    let arsipInstansi = '';
    let arsipKategori = '';
    let arsipTimer;

    function loadArsip(page) {
        arsipPage = page || 1;
        const params = new URLSearchParams({ page: arsipPage });
        if (arsipSearch) params.set('search', arsipSearch);
        if (arsipInstansi) params.set('instansi_id', arsipInstansi);
        if (arsipKategori) params.set('kategori', arsipKategori);

        fetch(`{{ route('admin.sinkro.arsip-pns.data') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                const tbody = document.getElementById('arsip-tbody');
                const meta = { current_page: res.current_page, last_page: res.last_page, total: res.total, from: res.from, to: res.to };

                document.getElementById('arsip-info').textContent = `Total: ${res.total} arsip PNS`;
                document.getElementById('arsip-showing').textContent = showingText(meta);

                if (!res.data.length) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Belum ada data. Sinkro arsip PNS terlebih dahulu.</td></tr>';
                    document.getElementById('arsip-pagination').innerHTML = '';
                    return;
                }

                let html = '';
                res.data.forEach((row, i) => {
                    const no = (res.current_page - 1) * res.per_page + i + 1;
                    const instansiNama = row.instansi ? row.instansi.nama : '-';
                    const skor = row.skor_arsip_2026 !== null ? parseFloat(row.skor_arsip_2026).toFixed(2) + '%' : '-';
                    html += `<tr>
                        <td>${no}</td>
                        <td><code>${row.nip || '-'}</code></td>
                        <td>${row.nama || '-'}</td>
                        <td><small>${instansiNama}</small></td>
                        <td>${kategoriBadge(row.kategori_kelengkapan_2026)}</td>
                        <td>${skor}</td>
                        <td><small>${formatDate(row.updated_at)}</small></td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                buildPagination(meta, 'arsip-pagination', loadArsip);
            })
            .catch(() => {
                document.getElementById('arsip-tbody').innerHTML =
                    '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            });
    }

    document.getElementById('search-arsip').addEventListener('input', function () {
        clearTimeout(arsipTimer);
        arsipTimer = setTimeout(() => { arsipSearch = this.value; loadArsip(1); }, 400);
    });

    document.getElementById('filter-instansi-arsip').addEventListener('change', function () {
        arsipInstansi = this.value; loadArsip(1);
    });

    document.getElementById('filter-kategori').addEventListener('change', function () {
        arsipKategori = this.value; loadArsip(1);
    });

    loadArsip(1);
});
</script>
@endpush
