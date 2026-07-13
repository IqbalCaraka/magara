@extends('layouts.admin')

@section('title', 'Scanning Dokumen - ' . $nip)

@php
    $sudahScan = $dokumens->pluck('jenis_dokumen')->toArray();

    // Dokumen tetap (single)
    $dokumenTetap = ['D2NIP', 'SK CPNS', 'SK PNS', 'SPMT CPNS', 'DRH CPNS'];

    // Jenjang ijazah
    $jenjangIjazah = ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3', 'Profesi'];

    // Golongan pangkat
    $golongan = [];
    foreach (['I', 'II', 'III', 'IV'] as $romawi) {
        $max = $romawi === 'IV' ? 'e' : 'd';
        foreach (range('a', $max) as $huruf) {
            $golongan[] = "{$romawi}/{$huruf}";
        }
    }
@endphp

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-upc-scan" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Scanning Dokumen</p>
                <h1 class="h3 mb-1">{{ $asn->nama }}</h1>
                <p class="text-muted mb-0">NIP: <code>{{ $nip }}</code></p>
            </div>
        </div>
        <a href="{{ route('admin.takah.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Flow Progress --}}
    <div class="d-flex gap-2 mt-2 mb-3 flex-wrap align-items-center">
        <span class="badge text-bg-success"><i class="bi bi-check-circle"></i> 1. Pencatatan Takah</span>
        <i class="bi bi-chevron-right text-muted"></i>
        <span class="badge text-bg-primary"><i class="bi bi-arrow-right-circle"></i> 2. Scanning Dokumen</span>
        <i class="bi bi-chevron-right text-muted"></i>
        <span class="badge text-bg-secondary"><i class="bi bi-circle"></i> 3. Daftar Isi Takah</span>
    </div>

    <div class="row g-3">
        {{-- Info ASN & Takah --}}
        <div class="col-12 col-lg-4">
            <section class="panel h-100">
                <div class="panel-header">
                    <h2 class="h5 mb-0 section-title"><i class="bi bi-person-badge" aria-hidden="true"></i><span>Info ASN</span></h2>
                </div>
                <div class="p-3">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:100px">Nama</td>
                            <td><strong>{{ $asn->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIP</td>
                            <td><code>{{ $nip }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Instansi</td>
                            <td>{{ $asn->instansi?->nama ?? '-' }}</td>
                        </tr>
                        @if($takah)
                        <tr>
                            <td class="text-muted">Kode Rak</td>
                            <td><code>{{ $takah->kode_rak }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Posisi</td>
                            <td>{{ $takah->posisi_takah }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </section>
        </div>

        {{-- Form Tambah Scanning --}}
        <div class="col-12 col-lg-8">
            <form action="{{ route('admin.scanning.store', $nip) }}" method="POST" id="form-scanning">
                @csrf

                @error('jenis_dokumen')
                    <div class="alert alert-danger small py-2">{{ $message }}</div>
                @enderror

                {{-- 1. Dokumen Tetap --}}
                <section class="panel mb-3">
                    <div class="panel-header">
                        <h2 class="h6 mb-0 section-title"><i class="bi bi-file-earmark-text" aria-hidden="true"></i><span>Dokumen Utama</span></h2>
                    </div>
                    <div class="p-3">
                        <div class="row g-2">
                            @foreach($dokumenTetap as $jenis)
                                @php $sudah = in_array($jenis, $sudahScan); @endphp
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input dok-check" type="checkbox"
                                            name="jenis_dokumen[]" value="{{ $jenis }}"
                                            id="dok-{{ Str::slug($jenis) }}"
                                            {{ $sudah ? 'disabled' : '' }}>
                                        <label class="form-check-label small {{ $sudah ? 'text-muted text-decoration-line-through' : '' }}"
                                            for="dok-{{ Str::slug($jenis) }}">
                                            {{ $jenis }}
                                            @if($sudah)
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- 2. Ijazah (multiple, dropdown jenjang) --}}
                <section class="panel mb-3">
                    <div class="panel-header">
                        <h2 class="h6 mb-0 section-title"><i class="bi bi-mortarboard" aria-hidden="true"></i><span>Ijazah</span></h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-ijazah">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="p-3">
                        @php
                            $ijazahSudah = collect($sudahScan)->filter(fn($d) => str_starts_with($d, 'Ijazah - '))->map(fn($d) => str_replace('Ijazah - ', '', $d))->values();
                        @endphp
                        @if($ijazahSudah->count())
                            <div class="mb-2">
                                <small class="text-muted">Sudah di-scan:</small>
                                @foreach($ijazahSudah as $j)
                                    <span class="badge text-bg-success me-1"><i class="bi bi-check"></i> {{ $j }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div id="ijazah-entries">
                            {{-- JS will add entries here --}}
                        </div>
                        <div class="text-muted small" id="ijazah-empty">Klik "Tambah" untuk menambah ijazah yang di-scan.</div>
                    </div>
                </section>

                {{-- 3. SK Pangkat (multiple, dropdown golongan) --}}
                <section class="panel mb-3">
                    <div class="panel-header">
                        <h2 class="h6 mb-0 section-title"><i class="bi bi-award" aria-hidden="true"></i><span>SK Pangkat</span></h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-pangkat">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="p-3">
                        @php
                            $pangkatSudah = collect($sudahScan)->filter(fn($d) => str_starts_with($d, 'SK Pangkat - '))->map(fn($d) => str_replace('SK Pangkat - ', '', $d))->values();
                        @endphp
                        @if($pangkatSudah->count())
                            <div class="mb-2">
                                <small class="text-muted">Sudah di-scan:</small>
                                @foreach($pangkatSudah as $p)
                                    <span class="badge text-bg-success me-1"><i class="bi bi-check"></i> {{ $p }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div id="pangkat-entries"></div>
                        <div class="text-muted small" id="pangkat-empty">Klik "Tambah" untuk menambah SK Pangkat yang di-scan.</div>
                    </div>
                </section>

                {{-- 4. Jabatan (multiple, free text) --}}
                <section class="panel mb-3">
                    <div class="panel-header">
                        <h2 class="h6 mb-0 section-title"><i class="bi bi-briefcase" aria-hidden="true"></i><span>Jabatan</span></h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-jabatan">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="p-3">
                        @php
                            $jabatanSudah = collect($sudahScan)->filter(fn($d) => str_starts_with($d, 'Jabatan - '))->map(fn($d) => str_replace('Jabatan - ', '', $d))->values();
                        @endphp
                        @if($jabatanSudah->count())
                            <div class="mb-2">
                                <small class="text-muted">Sudah di-scan:</small>
                                @foreach($jabatanSudah as $jab)
                                    <span class="badge text-bg-success me-1"><i class="bi bi-check"></i> {{ $jab }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div id="jabatan-entries"></div>
                        <div class="text-muted small" id="jabatan-empty">Klik "Tambah" untuk menambah jabatan yang di-scan.</div>
                    </div>
                </section>

                {{-- 5. Sertifikat Diklat (multiple, free text) --}}
                <section class="panel mb-3">
                    <div class="panel-header">
                        <h2 class="h6 mb-0 section-title"><i class="bi bi-patch-check" aria-hidden="true"></i><span>Sertifikat Diklat</span></h2>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-diklat">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="p-3">
                        @php
                            $diklatSudah = collect($sudahScan)->filter(fn($d) => str_starts_with($d, 'Sertifikat Diklat - '))->map(fn($d) => str_replace('Sertifikat Diklat - ', '', $d))->values();
                        @endphp
                        @if($diklatSudah->count())
                            <div class="mb-2">
                                <small class="text-muted">Sudah di-scan:</small>
                                @foreach($diklatSudah as $dk)
                                    <span class="badge text-bg-success me-1"><i class="bi bi-check"></i> {{ $dk }}</span>
                                @endforeach
                            </div>
                        @endif
                        <div id="diklat-entries"></div>
                        <div class="text-muted small" id="diklat-empty">Klik "Tambah" untuk menambah sertifikat diklat yang di-scan.</div>
                    </div>
                </section>

                {{-- Tombol Simpan --}}
                <div class="d-flex gap-2 align-items-center mb-3">
                    <button type="submit" class="btn btn-primary" id="btn-scan" disabled>
                        <i class="bi bi-upc-scan"></i> Simpan Scanning
                    </button>
                    <span class="text-muted small" id="scan-count">Pilih atau tambah dokumen untuk di-scan</span>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Dokumen yang Sudah Di-scan --}}
    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-list-check" aria-hidden="true"></i><span>Dokumen Sudah Di-scan</span></h2>
                <p class="text-muted mb-0">Total: {{ $dokumens->count() }} dokumen</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jenis Dokumen</th>
                        <th>Dicatat Oleh</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dokumens as $i => $dok)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $dok->jenis_dokumen }}</td>
                            <td><small>{{ $dok->creator?->nama ?? '-' }}</small></td>
                            <td><small>{{ $dok->date_created?->format('d M Y, H.i') ?? '-' }}</small></td>
                            <td>
                                <form action="{{ route('admin.scanning.destroy', $dok) }}" method="POST"
                                    onsubmit="return confirm('Hapus dokumen {{ addslashes($dok->jenis_dokumen) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada dokumen yang di-scan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Link ke step berikutnya --}}
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('admin.takah.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali ke Pencatatan Takah
        </a>
        <button class="btn btn-outline-secondary btn-sm" disabled title="Dalam pengembangan">
            <i class="bi bi-arrow-right"></i> Lanjut ke Daftar Isi Takah
        </button>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnScan = document.getElementById('btn-scan');
    const scanCount = document.getElementById('scan-count');
    let entryCounter = 0;

    // --- Count total selected items ---
    function updateCount() {
        const checkboxes = document.querySelectorAll('.dok-check:checked').length;
        const dynamicEntries = document.querySelectorAll('.dynamic-entry').length;
        const total = checkboxes + dynamicEntries;

        btnScan.disabled = total === 0;
        scanCount.textContent = total > 0
            ? `${total} dokumen dipilih`
            : 'Pilih atau tambah dokumen untuk di-scan';
    }

    document.querySelectorAll('.dok-check:not(:disabled)').forEach(cb => {
        cb.addEventListener('change', updateCount);
    });

    // --- Generic: create a row with select/input + remove button ---
    function addDropdownEntry(containerId, emptyId, prefix, options, placeholder) {
        const container = document.getElementById(containerId);
        document.getElementById(emptyId).style.display = 'none';
        entryCounter++;

        const row = document.createElement('div');
        row.className = 'd-flex gap-2 mb-2 align-items-center dynamic-entry';
        row.dataset.id = entryCounter;

        let selectHtml = `<select class="form-select form-select-sm flex-grow-1" name="jenis_dokumen[]" required>`;
        selectHtml += `<option value="">-- ${placeholder} --</option>`;
        options.forEach(opt => {
            selectHtml += `<option value="${prefix} - ${opt}">${opt}</option>`;
        });
        selectHtml += `</select>`;

        row.innerHTML = `
            ${selectHtml}
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-entry" title="Hapus">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        container.appendChild(row);

        row.querySelector('.btn-remove-entry').addEventListener('click', function () {
            row.remove();
            if (!container.children.length) {
                document.getElementById(emptyId).style.display = '';
            }
            updateCount();
        });

        // When select changes to empty, don't count it
        row.querySelector('select').addEventListener('change', function () {
            if (!this.value) {
                row.classList.remove('dynamic-entry');
            } else {
                row.classList.add('dynamic-entry');
            }
            updateCount();
        });

        updateCount();
    }

    function addTextEntry(containerId, emptyId, prefix, placeholder) {
        const container = document.getElementById(containerId);
        document.getElementById(emptyId).style.display = 'none';
        entryCounter++;

        const row = document.createElement('div');
        row.className = 'd-flex gap-2 mb-2 align-items-center dynamic-entry';
        row.dataset.id = entryCounter;

        row.innerHTML = `
            <input type="text" class="form-control form-control-sm flex-grow-1 entry-text"
                placeholder="${placeholder}" required>
            <input type="hidden" name="jenis_dokumen[]" class="entry-hidden" value="">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-entry" title="Hapus">
                <i class="bi bi-x-lg"></i>
            </button>
        `;

        container.appendChild(row);

        const textInput = row.querySelector('.entry-text');
        const hiddenInput = row.querySelector('.entry-hidden');

        textInput.addEventListener('input', function () {
            hiddenInput.value = this.value.trim() ? `${prefix} - ${this.value.trim()}` : '';
            if (!this.value.trim()) {
                row.classList.remove('dynamic-entry');
            } else {
                row.classList.add('dynamic-entry');
            }
            updateCount();
        });

        row.querySelector('.btn-remove-entry').addEventListener('click', function () {
            row.remove();
            if (!container.children.length) {
                document.getElementById(emptyId).style.display = '';
            }
            updateCount();
        });

        textInput.focus();
        updateCount();
    }

    // --- Ijazah ---
    const jenjangOptions = @json($jenjangIjazah);
    document.getElementById('btn-add-ijazah').addEventListener('click', function () {
        addDropdownEntry('ijazah-entries', 'ijazah-empty', 'Ijazah', jenjangOptions, 'Pilih Jenjang');
    });

    // --- SK Pangkat ---
    const golonganOptions = @json($golongan);
    document.getElementById('btn-add-pangkat').addEventListener('click', function () {
        addDropdownEntry('pangkat-entries', 'pangkat-empty', 'SK Pangkat', golonganOptions, 'Pilih Golongan');
    });

    // --- Jabatan ---
    document.getElementById('btn-add-jabatan').addEventListener('click', function () {
        addTextEntry('jabatan-entries', 'jabatan-empty', 'Jabatan', 'Nama jabatan, contoh: Kepala Seksi...');
    });

    // --- Sertifikat Diklat ---
    document.getElementById('btn-add-diklat').addEventListener('click', function () {
        addTextEntry('diklat-entries', 'diklat-empty', 'Sertifikat Diklat', 'Nama diklat, contoh: Prajabatan...');
    });

    // --- Validate before submit: remove empty entries ---
    document.getElementById('form-scanning').addEventListener('submit', function (e) {
        // Remove hidden inputs with empty values
        document.querySelectorAll('.entry-hidden').forEach(h => {
            if (!h.value) h.closest('.dynamic-entry')?.remove();
        });
        // Remove selects with empty values
        document.querySelectorAll('.dynamic-entry select').forEach(sel => {
            if (!sel.value) sel.closest('.dynamic-entry')?.remove();
        });

        // Check if anything selected
        const total = document.querySelectorAll('.dok-check:checked').length +
                      document.querySelectorAll('.dynamic-entry').length;
        if (total === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 dokumen untuk di-scan.');
        }
    });
});
</script>
@endpush
