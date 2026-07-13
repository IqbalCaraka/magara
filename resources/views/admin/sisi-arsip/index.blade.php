@extends('layouts.admin')

@section('title', 'Daftar Isi Takah')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-folder2-open" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Arsip</p>
                <h1 class="h3 mb-1">Daftar Isi Takah</h1>
                <p class="text-muted mb-0">Pencatatan daftar isi tata naskah kepegawaian.</p>
            </div>
        </div>
    </div>

    {{-- Flow --}}
    <div class="d-flex gap-2 mt-2 mb-3 flex-wrap align-items-center">
        <a href="{{ route('admin.takah.index') }}" class="badge text-bg-success text-decoration-none">1. Pencatatan Takah</a>
        <i class="bi bi-chevron-right text-muted"></i>
        <span class="badge text-bg-success">2. Scanning Dokumen</span>
        <i class="bi bi-chevron-right text-muted"></i>
        <span class="badge text-bg-primary">3. Daftar Isi Takah</span>
    </div>

    <section class="panel mt-3">
        <div class="p-4 text-center text-muted">
            <i class="bi bi-cone-striped display-4 d-block mb-3"></i>
            <h3 class="h5">Fitur Dalam Pengembangan</h3>
            <p class="mb-0">Menu Daftar Isi Takah sedang dalam tahap pengembangan dan akan segera tersedia.</p>
            <p class="text-muted small mt-2">Fitur ini merupakan langkah ke-3 setelah Pencatatan Takah dan Scanning Dokumen.</p>
        </div>
    </section>
@endsection
