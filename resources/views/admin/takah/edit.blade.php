@extends('layouts.admin')

@section('title', 'Edit Pencatatan Takah')

@php
    // Parse kode_rak: 00-02-04-020-1-2
    $parts = explode('-', $takah->kode_rak);
    $pKantor = $parts[0] ?? '00';
    $pGedung = $parts[1] ?? '02';
    $pLantai = $parts[2] ?? '';
    $pRollpack = $parts[3] ?? '';
    $pLemari = $parts[4] ?? '';
    $pRak = $parts[5] ?? '';
@endphp

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-journal-text" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Pencatatan Takah</p>
                <h1 class="h3 mb-1">Edit Penempatan</h1>
                <p class="text-muted mb-0">Perbarui data pencatatan tata naskah kepegawaian.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.takah.update', $takah) }}" method="POST" id="form-takah">
        @csrf
        @method('PUT')
        <input type="hidden" name="nip" value="{{ $takah->nip }}">
        <input type="hidden" name="kode_rak" id="input-kode-rak">
        <input type="hidden" name="posisi_takah" id="input-posisi-takah">

        <div class="row g-3 mt-1">
            {{-- Kolom Kiri: ASN (Locked) --}}
            <div class="col-12 col-lg-5">
                <section class="panel h-100">
                    <div class="panel-header">
                        <h2 class="h5 mb-0 section-title"><i class="bi bi-person-badge" aria-hidden="true"></i><span>Data ASN</span></h2>
                    </div>
                    <div class="p-3">
                        <div class="p-3 border rounded bg-light">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10" style="width:48px;height:48px">
                                        <i class="bi bi-person-fill text-primary fs-4"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong class="d-block">{{ $asn?->nama ?? '-' }}</strong>
                                    <code>{{ $takah->nip }}</code>
                                    <small class="d-block text-muted mt-1">{{ $asn?->instansi?->nama ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-lock"></i> NIP tidak dapat diubah. Buat pencatatan baru untuk NIP lain.</small>

                        {{-- Status Generate --}}
                        <div class="mt-3">
                            <label class="form-label form-label-sm fw-semibold">Status Generate</label>
                            <select class="form-select form-select-sm" name="status">
                                <option value="0" {{ !$takah->status ? 'selected' : '' }}>Belum Generate</option>
                                <option value="1" {{ $takah->status ? 'selected' : '' }}>Sudah Generate</option>
                            </select>
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
                                    <option value="03" {{ $pLantai === '03' ? 'selected' : '' }}>03</option>
                                    <option value="04" {{ $pLantai === '04' ? 'selected' : '' }}>04</option>
                                    <option value="05" {{ $pLantai === '05' ? 'selected' : '' }}>05</option>
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
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ $pLemari == (string)$i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Rak --}}
                            <div class="col-12 col-sm-6">
                                <label class="form-label form-label-sm fw-semibold">Rak</label>
                                <select class="form-select form-select-sm rak-select" id="sel-rak">
                                    <option value="">-- Pilih Rak --</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ $pRak == (string)$i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        {{-- Preview Kode Rak --}}
                        <div class="mt-3 p-2 border rounded bg-light">
                            <small class="text-muted d-block mb-1">Kode Rak:</small>
                            <strong class="font-monospace fs-5" id="preview-kode-rak">{{ $takah->kode_rak }}</strong>
                        </div>

                        {{-- Posisi Takah --}}
                        <div class="mt-3">
                            <label class="form-label form-label-sm fw-semibold">Posisi Takah</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="chk-sama-kode-rak" {{ $takah->posisi_takah === $takah->kode_rak ? 'checked' : '' }}>
                                <label class="form-check-label small" for="chk-sama-kode-rak">Sama dengan Kode Rak</label>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="txt-posisi-takah" placeholder="Contoh: A-1-1" value="{{ $takah->posisi_takah }}" {{ $takah->posisi_takah === $takah->kode_rak ? 'readonly' : '' }}>
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
            <button type="submit" class="btn btn-primary" id="btn-simpan">
                <i class="bi bi-check-lg"></i> Simpan Perubahan
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selKantor = document.getElementById('sel-kantor');
    const selGedung = document.getElementById('sel-gedung');
    const selLantai = document.getElementById('sel-lantai');
    const selRollpack = document.getElementById('sel-rollpack');
    const selLemari = document.getElementById('sel-lemari');
    const selRak = document.getElementById('sel-rak');
    const previewKodeRak = document.getElementById('preview-kode-rak');
    const chkSamaKodeRak = document.getElementById('chk-sama-kode-rak');
    const txtPosisiTakah = document.getElementById('txt-posisi-takah');
    const inputKodeRak = document.getElementById('input-kode-rak');
    const inputPosisiTakah = document.getElementById('input-posisi-takah');
    const btnSimpan = document.getElementById('btn-simpan');

    const existingRollpack = '{{ $pRollpack }}';

    // Populate Rollpack 1-200
    for (let i = 1; i <= 200; i++) {
        const opt = document.createElement('option');
        const val = String(i).padStart(3, '0');
        opt.value = val;
        opt.textContent = i;
        if (val === existingRollpack) opt.selected = true;
        selRollpack.appendChild(opt);
    }

    // Kode Rak Preview
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

    // Posisi checkbox
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

    function validateForm() {
        const lantaiOk = !!selLantai.value;
        const rollpackOk = !!selRollpack.value;
        const lemariOk = !!selLemari.value;
        const rakOk = !!selRak.value;
        const posisiOk = !!txtPosisiTakah.value.trim();

        btnSimpan.disabled = !(lantaiOk && rollpackOk && lemariOk && rakOk && posisiOk);
    }

    // Form Submit
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

    updateKodeRak();
});
</script>
@endpush
