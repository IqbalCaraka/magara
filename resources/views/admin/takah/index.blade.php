@extends('layouts.admin')

@section('title', 'Pencatatan Takah')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Pencatatan</p>
                <h1 class="h3 mb-1">Tata Naskah (Takah)</h1>
                <p class="text-muted mb-0">Kelola pencatatan tata naskah kepegawaian.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#modalExport">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </button>
            <a href="{{ route('admin.takah.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Pencatatan
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-3 mt-1">
        <div class="col-6 col-md-3">
            <div class="panel p-3 text-center h-100">
                <div class="h3 mb-1 text-primary">{{ $totalTakah }}</div>
                <small class="text-muted">Total Pencatatan</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="panel p-3 text-center h-100 cursor-pointer" onclick="setStepFilter('takah_only')" role="button">
                <div class="h3 mb-1 text-warning">{{ $nipsTakahOnly }}</div>
                <small class="text-muted">Belum Scanning</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="panel p-3 text-center h-100 cursor-pointer" onclick="setStepFilter('scanned')" role="button">
                <div class="h3 mb-1 text-success">{{ $nipsWithScanning }}</div>
                <small class="text-muted">Sudah Scanning</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="panel p-3 text-center h-100">
                <div class="h3 mb-1 text-secondary">{{ $nipsWithDaftarIsi }}</div>
                <small class="text-muted">Daftar Isi Takah</small>
            </div>
        </div>
    </div>

    {{-- Tabel Pencatatan Takah --}}
    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-journal-text" aria-hidden="true"></i><span>Data Pencatatan Takah</span></h2>
                <p class="text-muted mb-0" id="takah-info">Memuat data...</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <select class="form-select form-select-sm" style="max-width:220px" id="filter-instansi">
                    <option value="">Semua Instansi</option>
                    @foreach($instansiList as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->nama }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm" style="max-width:180px" id="filter-step">
                    <option value="">Semua Progress</option>
                    <option value="takah_only">Belum Scanning</option>
                    <option value="scanned">Sudah Scanning</option>
                </select>
                <select class="form-select form-select-sm" style="max-width:180px" id="filter-status">
                    <option value="">Semua Status</option>
                    <option value="0">Belum Generate</option>
                    <option value="1">Sudah Generate</option>
                </select>
                <input class="form-control form-control-sm" style="max-width:200px" type="search" placeholder="Cari NIP/Nama/Rak..." id="search-takah">
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NIP / Nama</th>
                        <th>Instansi</th>
                        <th>Kode Rak</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Dicatat Oleh</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="takah-tbody">
                    <tr><td colspan="9" class="text-center text-muted py-4">Memuat...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mt-3 px-3 pb-3">
            <p class="text-muted small mb-0" id="takah-showing"></p>
            <nav id="takah-pagination"></nav>
        </div>
    </section>

    {{-- Modal Export Excel --}}
    <div class="modal fade" id="modalExport" tabindex="-1" aria-labelledby="modalExportLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.takah.export-excel') }}" method="POST" id="form-export">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalExportLabel">
                            <i class="bi bi-file-earmark-excel text-success"></i> Export Excel Pencatatan Takah
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="only_belum_generate" value="1" id="chk-belum-generate" checked>
                                <label class="form-check-label" for="chk-belum-generate">
                                    Hanya yang <span class="badge text-bg-warning">Belum Generate</span>
                                </label>
                            </div>
                            <small class="text-muted">Jika dicentang, hanya data yang belum pernah di-export yang akan diambil.</small>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Instansi</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="chk-semua-instansi" checked>
                                <label class="form-check-label fw-semibold" for="chk-semua-instansi">Seluruh Instansi</label>
                            </div>
                            <div id="instansi-list-wrap">
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" id="search-instansi-export" placeholder="Cari instansi..." disabled>
                                </div>
                                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="instansi-checkboxes">
                                    @foreach($instansiList as $inst)
                                        <div class="form-check">
                                            <input class="form-check-input instansi-cb" type="checkbox" name="instansi_ids[]" value="{{ $inst->id }}" id="inst-{{ $inst->id }}" checked disabled>
                                            <label class="form-check-label small" for="inst-{{ $inst->id }}">{{ $inst->nama }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success btn-sm" id="btn-export">
                            <i class="bi bi-download"></i> <span>Download Excel</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .cursor-pointer:hover { opacity: 0.85; }
    .progress-flow { display: flex; gap: 4px; align-items: center; }
    .step-dot {
        width: 22px; height: 22px; border-radius: 50%;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 10px; color: #fff; font-weight: 700;
    }
    .step-dot.done { background: #198754; }
    .step-dot.current { background: #0d6efd; animation: pulse-step 1.5s infinite; }
    .step-dot.pending { background: #dee2e6; color: #6c757d; }
    .step-line { width: 12px; height: 2px; }
    .step-line.done { background: #198754; }
    .step-line.pending { background: #dee2e6; }
    @keyframes pulse-step { 0%,100% { opacity:1; } 50% { opacity:.6; } }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function buildPagination(meta, containerId, callback) {
        const nav = document.getElementById(containerId);
        if (meta.last_page <= 1) { nav.innerHTML = ''; return; }

        let html = '<ul class="pagination pagination-sm mb-0">';
        html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${meta.current_page - 1}">Previous</a></li>`;

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
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function progressFlow(scanCount) {
        // Step 1: Takah - selalu done (karena sudah ada di tabel)
        // Step 2: Scanning - done jika scan_count > 0
        // Step 3: Daftar Isi - selalu pending (belum ada fitur)
        const s2 = scanCount > 0;

        const dot1 = '<span class="step-dot done" title="Pencatatan Takah">1</span>';
        const line1 = `<span class="step-line ${s2 ? 'done' : 'pending'}"></span>`;
        const dot2Class = s2 ? 'done' : 'current';
        const dot2Title = s2 ? `Scanning (${scanCount} dok)` : 'Belum Scanning';
        const dot2 = `<span class="step-dot ${dot2Class}" title="${dot2Title}">2</span>`;
        const line2 = '<span class="step-line pending"></span>';
        const dot3 = '<span class="step-dot pending" title="Daftar Isi Takah">3</span>';

        let label = '';
        if (!s2) {
            label = '<br><small class="text-warning">Menunggu Scanning</small>';
        } else {
            label = `<br><small class="text-success">${scanCount} dok di-scan</small>`;
        }

        return `<div class="progress-flow">${dot1}${line1}${dot2}${line2}${dot3}</div>${label}`;
    }

    // --- DATA TABLE ---
    let takahPage = 1;
    let takahSearch = '';
    let takahStatus = '';
    let takahInstansi = '';
    let takahStep = '';
    let takahTimer;

    function loadTakah(page) {
        takahPage = page || 1;
        const params = new URLSearchParams({ page: takahPage });
        if (takahSearch) params.set('search', takahSearch);
        if (takahStatus !== '') params.set('status', takahStatus);
        if (takahInstansi) params.set('instansi_ids', takahInstansi);
        if (takahStep) params.set('step', takahStep);

        fetch(`{{ route('admin.takah.data') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                const tbody = document.getElementById('takah-tbody');
                const meta = { current_page: res.current_page, last_page: res.last_page, total: res.total, from: res.from, to: res.to };

                document.getElementById('takah-info').textContent = `Total: ${res.total} pencatatan`;
                document.getElementById('takah-showing').textContent = showingText(meta);

                if (!res.data.length) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">Belum ada data pencatatan takah.</td></tr>';
                    document.getElementById('takah-pagination').innerHTML = '';
                    return;
                }

                let html = '';
                res.data.forEach((row, i) => {
                    const no = (res.current_page - 1) * res.per_page + i + 1;
                    const asnNama = row.asn_nama || '-';
                    const instansiNama = row.instansi_nama || '-';
                    const scanCount = parseInt(row.scan_count) || 0;
                    const statusBadge = row.status
                        ? '<span class="badge text-bg-success">Sudah Generate</span>'
                        : '<span class="badge text-bg-warning">Belum Generate</span>';

                    const creator = row.creator ? row.creator.nama : '-';

                    html += `<tr>
                        <td>${no}</td>
                        <td>
                            <strong class="d-block small">${asnNama}</strong>
                            <code class="small">${row.nip}</code>
                        </td>
                        <td><small>${instansiNama}</small></td>
                        <td>
                            <code class="small">${row.kode_rak}</code>
                            <br><small class="text-muted">${row.posisi_takah}</small>
                        </td>
                        <td>${progressFlow(scanCount)}</td>
                        <td>${statusBadge}</td>
                        <td><small>${creator}</small></td>
                        <td><small>${formatDate(row.date_created)}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="/admin/scanning/${row.nip}" class="btn btn-sm btn-outline-success" title="Scanning Dokumen">
                                    <i class="bi bi-upc-scan"></i>
                                </a>
                                <a href="/admin/takah/${row.id}/edit" class="btn btn-sm btn-outline-primary" title="Edit Takah">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="/admin/takah/${row.id}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                buildPagination(meta, 'takah-pagination', loadTakah);
            })
            .catch(() => {
                document.getElementById('takah-tbody').innerHTML =
                    '<tr><td colspan="9" class="text-center text-danger py-4">Gagal memuat data.</td></tr>';
            });
    }

    document.getElementById('search-takah').addEventListener('input', function () {
        clearTimeout(takahTimer);
        takahTimer = setTimeout(() => { takahSearch = this.value; loadTakah(1); }, 400);
    });

    document.getElementById('filter-status').addEventListener('change', function () {
        takahStatus = this.value; loadTakah(1);
    });

    document.getElementById('filter-instansi').addEventListener('change', function () {
        takahInstansi = this.value; loadTakah(1);
    });

    document.getElementById('filter-step').addEventListener('change', function () {
        takahStep = this.value; loadTakah(1);
    });

    // Global function for summary card click
    window.setStepFilter = function(step) {
        const sel = document.getElementById('filter-step');
        sel.value = step;
        takahStep = step;
        loadTakah(1);
    };

    loadTakah(1);

    // --- EXPORT MODAL LOGIC ---
    const chkSemua = document.getElementById('chk-semua-instansi');
    const instansiCbs = document.querySelectorAll('.instansi-cb');
    const searchInstansiExport = document.getElementById('search-instansi-export');

    chkSemua.addEventListener('change', function () {
        if (this.checked) {
            instansiCbs.forEach(cb => { cb.checked = true; cb.disabled = true; });
            searchInstansiExport.disabled = true;
        } else {
            instansiCbs.forEach(cb => { cb.disabled = false; cb.checked = false; });
            searchInstansiExport.disabled = false;
        }
    });

    searchInstansiExport.addEventListener('input', function () {
        const val = this.value.toLowerCase();
        document.querySelectorAll('#instansi-checkboxes .form-check').forEach(div => {
            const label = div.querySelector('label').textContent.toLowerCase();
            div.style.display = label.includes(val) ? '' : 'none';
        });
    });

    const btnExport = document.getElementById('btn-export');
    document.getElementById('form-export').addEventListener('submit', function (e) {
        e.preventDefault();

        btnExport.disabled = true;
        btnExport.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating...';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');

        if (document.getElementById('chk-belum-generate').checked) {
            formData.append('only_belum_generate', '1');
        }

        if (!chkSemua.checked) {
            instansiCbs.forEach(cb => {
                if (cb.checked) formData.append('instansi_ids[]', cb.value);
            });
        }

        fetch('{{ route("admin.takah.export-excel") }}', { method: 'POST', body: formData })
        .then(res => {
            if (!res.ok) return res.text().then(t => { throw new Error(t); });
            const disposition = res.headers.get('Content-Disposition');
            let filename = 'pencatatan_takah.xlsx';
            if (disposition) {
                const match = disposition.match(/filename="?(.+?)"?$/);
                if (match) filename = match[1];
            }
            return res.blob().then(blob => ({ blob, filename }));
        })
        .then(({ blob, filename }) => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = filename;
            document.body.appendChild(a); a.click(); a.remove();
            window.URL.revokeObjectURL(url);
            bootstrap.Modal.getInstance(document.getElementById('modalExport')).hide();
            loadTakah(takahPage);
        })
        .catch(() => { alert('Gagal export: tidak ada data atau terjadi kesalahan.'); })
        .finally(() => {
            btnExport.disabled = false;
            btnExport.innerHTML = '<i class="bi bi-download"></i> <span>Download Excel</span>';
        });
    });
});
</script>
@endpush
