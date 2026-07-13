@extends('layouts.admin')

@section('title', 'Tambah Pencatatan Takah')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-journal-plus" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Pencatatan Takah</p>
                <h1 class="h3 mb-1">Penempatan Manual</h1>
                <p class="text-muted mb-0">Catat posisi tata naskah kepegawaian berdasarkan lokasi rak BKN.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.takah.store') }}" method="POST" id="form-takah">
        @csrf
        <input type="hidden" name="nip" id="input-nip">
        <input type="hidden" name="kode_rak" id="input-kode-rak">
        <input type="hidden" name="posisi_takah" id="input-posisi-takah">

        <div class="row g-3 mt-1">
            {{-- Kolom Kiri: Cari ASN --}}
            <div class="col-12 col-lg-5">
                <section class="panel h-100">
                    <div class="panel-header">
                        <h2 class="h5 mb-0 section-title"><i class="bi bi-person-search" aria-hidden="true"></i><span>Cari ASN (NIP)</span></h2>
                    </div>
                    <div class="p-3">
                        {{-- Filter Instansi --}}
                        <div class="mb-3">
                            <label class="form-label form-label-sm" for="filter-instansi">Instansi</label>
                            <select class="form-select form-select-sm" id="filter-instansi">
                                <option value="">-- Semua Instansi --</option>
                                @foreach($instansiList as $inst)
                                    <option value="{{ $inst->id }}">{{ $inst->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search NIP --}}
                        <div class="mb-3">
                            <label class="form-label form-label-sm" for="search-nip">Cari NIP / Nama</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="search-nip" placeholder="Masukkan NIP atau Nama...">
                                <button class="btn btn-primary" type="button" id="btn-cari">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                            </div>
                        </div>

                        {{-- Hasil Pencarian --}}
                        <div id="hasil-cari">
                            <div class="text-center text-muted py-4" id="placeholder-cari">
                                <i class="bi bi-person-badge display-6 d-block mb-2"></i>
                                <small>Cari ASN terlebih dahulu</small>
                            </div>
                            <div class="list-group list-group-flush d-none" id="list-asn"></div>
                            <div class="d-none mt-2" id="asn-pagination-wrap">
                                <nav id="asn-pagination"></nav>
                            </div>
                        </div>

                        {{-- ASN Terpilih --}}
                        <div class="d-none mt-3 p-3 border rounded bg-light" id="asn-selected">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong class="d-block" id="sel-nama"></strong>
                                    <code id="sel-nip"></code>
                                    <small class="d-block text-muted" id="sel-instansi"></small>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btn-reset-asn" title="Ganti ASN">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- Kolom Kanan: Lokasi Rak BKN --}}
            <div class="col-12 col-lg-7">
                <section class="panel h-100">
                    <div class="panel-header">
                        <h2 class="h5 mb-0 section-title"><i class="bi bi-geo-alt" aria-hidden="true"></i><span>Pilih Lokasi Rak BKN</span></h2>
                    </div>
                    <div class="p-3">
                        <div class="row g-3">
                            {{-- Kantor Pusat --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Kantor Pusat</label>
                                <select class="form-select form-select-sm rak-select" id="sel-kantor" disabled>
                                    <option value="00" selected>00 - Kantor Pusat</option>
                                </select>
                            </div>

                            {{-- Gedung --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Gedung</label>
                                <select class="form-select form-select-sm rak-select" id="sel-gedung">
                                    <option value="02" selected>02</option>
                                </select>
                            </div>

                            {{-- Lantai --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Lantai</label>
                                <select class="form-select form-select-sm rak-select" id="sel-lantai">
                                    <option value="">-- Pilih Lantai --</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                </select>
                            </div>

                            {{-- Rollpack / Ruangan --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Rollpack / Ruangan</label>
                                <select class="form-select form-select-sm rak-select" id="sel-rollpack">
                                    <option value="">-- Pilih Rollpack --</option>
                                </select>
                            </div>

                            {{-- Lemari --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Lemari</label>
                                <select class="form-select form-select-sm rak-select" id="sel-lemari">
                                    <option value="">-- Pilih Lemari --</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                </select>
                            </div>

                            {{-- Rak --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Rak</label>
                                <select class="form-select form-select-sm rak-select" id="sel-rak">
                                    <option value="">-- Pilih Rak --</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>

                        {{-- Preview Kode Rak --}}
                        <div class="mt-3 p-2 border rounded bg-light">
                            <small class="text-muted d-block mb-1">Kode Rak:</small>
                            <strong class="font-monospace fs-5" id="preview-kode-rak">00-02-__-___-_-_</strong>
                        </div>

                        {{-- Posisi Takah --}}
                        <div class="mt-3">
                            <label class="form-label form-label-sm fw-semibold">Posisi Takah</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="chk-sama-kode-rak">
                                <label class="form-check-label small" for="chk-sama-kode-rak">Sama dengan Kode Rak</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="txt-posisi-takah" placeholder="Contoh: A-1-1">
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="d-flex gap-2 mt-3 justify-content-end">
            <a href="{{ route('admin.takah.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary" id="btn-simpan" disabled>
                <i class="bi bi-check-lg"></i> Simpan Penempatan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchNip = document.getElementById('search-nip');
    const btnCari = document.getElementById('btn-cari');
    const filterInstansi = document.getElementById('filter-instansi');
    const listAsn = document.getElementById('list-asn');
    const placeholderCari = document.getElementById('placeholder-cari');
    const asnPaginationWrap = document.getElementById('asn-pagination-wrap');
    const asnSelected = document.getElementById('asn-selected');
    const btnResetAsn = document.getElementById('btn-reset-asn');
    const btnSimpan = document.getElementById('btn-simpan');

    // Rak selects
    const selKantor = document.getElementById('sel-kantor');
    const selGedung = document.getElementById('sel-gedung');
    const selLantai = document.getElementById('sel-lantai');
    const selRollpack = document.getElementById('sel-rollpack');
    const selLemari = document.getElementById('sel-lemari');
    const selRak = document.getElementById('sel-rak');
    const previewKodeRak = document.getElementById('preview-kode-rak');

    // Posisi
    const chkSamaKodeRak = document.getElementById('chk-sama-kode-rak');
    const txtPosisiTakah = document.getElementById('txt-posisi-takah');

    // Hidden inputs
    const inputNip = document.getElementById('input-nip');
    const inputKodeRak = document.getElementById('input-kode-rak');
    const inputPosisiTakah = document.getElementById('input-posisi-takah');

    let selectedNip = '';

    // --- Populate Rollpack 1-200 ---
    for (let i = 1; i <= 200; i++) {
        const opt = document.createElement('option');
        const val = String(i).padStart(3, '0');
        opt.value = val;
        opt.textContent = i;
        selRollpack.appendChild(opt);
    }

    // --- Searchable rollpack ---
    let rollpackSearch = '';
    selRollpack.addEventListener('focus', function () {
        this.dataset.open = '1';
    });

    // --- Kode Rak Preview ---
    function updateKodeRak() {
        const kantor = selKantor.value || '__';
        const gedung = selGedung.value || '__';
        const lantai = selLantai.value || '__';
        const rollpack = selRollpack.value || '___';
        const lemari = selLemari.value || '_';
        const rak = selRak.value || '_';

        const kode = `${kantor}-${gedung}-${lantai}-${rollpack}-${lemari}-${rak}`;
        previewKodeRak.textContent = kode;

        if (chkSamaKodeRak.checked) {
            txtPosisiTakah.value = kode;
        }

        validateForm();
    }

    document.querySelectorAll('.rak-select').forEach(sel => {
        sel.addEventListener('change', updateKodeRak);
    });

    // --- Posisi checkbox ---
    chkSamaKodeRak.addEventListener('change', function () {
        if (this.checked) {
            txtPosisiTakah.value = previewKodeRak.textContent;
            txtPosisiTakah.readOnly = true;
        } else {
            txtPosisiTakah.readOnly = false;
        }
        validateForm();
    });

    txtPosisiTakah.addEventListener('input', validateForm);

    // --- Validate Form ---
    function validateForm() {
        const nipOk = !!selectedNip;
        const lantaiOk = !!selLantai.value;
        const rollpackOk = !!selRollpack.value;
        const lemariOk = !!selLemari.value;
        const rakOk = !!selRak.value;
        const posisiOk = !!txtPosisiTakah.value.trim();

        btnSimpan.disabled = !(nipOk && lantaiOk && rollpackOk && lemariOk && rakOk && posisiOk);
    }

    // --- Cari ASN ---
    function loadAsn(page) {
        const params = new URLSearchParams({ page: page || 1 });
        const search = searchNip.value.trim();
        if (search) params.set('search', search);
        if (filterInstansi.value) params.set('instansi_id', filterInstansi.value);

        fetch(`{{ route('admin.takah.cari-asn') }}?${params}`)
            .then(r => r.json())
            .then(res => {
                placeholderCari.classList.add('d-none');
                listAsn.classList.remove('d-none');

                if (!res.data.length) {
                    listAsn.innerHTML = '<div class="text-center text-muted py-3"><small>Tidak ditemukan.</small></div>';
                    asnPaginationWrap.classList.add('d-none');
                    return;
                }

                let html = '';
                res.data.forEach(row => {
                    const instNama = row.instansi ? row.instansi.nama : '-';
                    html += `<button type="button" class="list-group-item list-group-item-action py-2 asn-item"
                                data-nip="${row.nip}" data-nama="${row.nama}" data-instansi="${instNama}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="d-block small">${row.nama}</strong>
                                <code class="small">${row.nip || '-'}</code>
                            </div>
                            <small class="text-muted text-end" style="max-width:40%">${instNama}</small>
                        </div>
                    </button>`;
                });
                listAsn.innerHTML = html;

                // Bind click
                listAsn.querySelectorAll('.asn-item').forEach(btn => {
                    btn.addEventListener('click', function () {
                        selectAsn(this.dataset.nip, this.dataset.nama, this.dataset.instansi);
                    });
                });

                // Pagination
                const meta = { current_page: res.current_page, last_page: res.last_page, total: res.total };
                if (meta.last_page > 1) {
                    asnPaginationWrap.classList.remove('d-none');
                    buildMiniPagination(meta, 'asn-pagination', loadAsn);
                } else {
                    asnPaginationWrap.classList.add('d-none');
                }
            })
            .catch(() => {
                listAsn.innerHTML = '<div class="text-center text-danger py-3"><small>Gagal memuat data.</small></div>';
                listAsn.classList.remove('d-none');
            });
    }

    function selectAsn(nip, nama, instansi) {
        selectedNip = nip;
        inputNip.value = nip;
        document.getElementById('sel-nama').textContent = nama;
        document.getElementById('sel-nip').textContent = nip;
        document.getElementById('sel-instansi').textContent = instansi;

        asnSelected.classList.remove('d-none');
        listAsn.classList.add('d-none');
        asnPaginationWrap.classList.add('d-none');
        placeholderCari.classList.add('d-none');

        validateForm();
    }

    btnResetAsn.addEventListener('click', function () {
        selectedNip = '';
        inputNip.value = '';
        asnSelected.classList.add('d-none');
        placeholderCari.classList.remove('d-none');
        validateForm();
    });

    btnCari.addEventListener('click', () => loadAsn(1));
    searchNip.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); loadAsn(1); }
    });
    filterInstansi.addEventListener('change', () => {
        if (searchNip.value.trim() || filterInstansi.value) loadAsn(1);
    });

    // --- Mini Pagination ---
    function buildMiniPagination(meta, containerId, callback) {
        const nav = document.getElementById(containerId);
        let html = '<ul class="pagination pagination-sm mb-0 justify-content-center">';
        html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${meta.current_page - 1}">&laquo;</a></li>`;

        let start = Math.max(1, meta.current_page - 2);
        let end = Math.min(meta.last_page, meta.current_page + 2);
        for (let i = start; i <= end; i++) {
            html += `<li class="page-item ${i === meta.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${meta.current_page + 1}">&raquo;</a></li>`;
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

    // --- Form Submit ---
    document.getElementById('form-takah').addEventListener('submit', function (e) {
        const kode = previewKodeRak.textContent;
        if (kode.includes('_')) {
            e.preventDefault();
            alert('Lengkapi semua pilihan lokasi rak.');
            return;
        }
        inputKodeRak.value = kode;
        inputPosisiTakah.value = txtPosisiTakah.value.trim();
    });
});
</script>
@endpush
