@extends('layouts.admin')

@section('title', 'Ganti Password')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-shield-lock" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Akun</p>
                <h1 class="h3 mb-1">Ganti Password</h1>
                <p class="text-muted mb-0">Ubah password akun Anda.</p>
            </div>
        </div>
    </div>

    <section class="panel mt-3" style="max-width: 500px;">
        <div class="panel-header">
            <h2 class="h5 mb-0 section-title"><i class="bi bi-key" aria-hidden="true"></i><span>Form Ganti Password</span></h2>
        </div>
        <div class="p-3">
            <form action="{{ route('admin.update-password') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="current_password">Password Lama</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan Password
                </button>
            </form>
        </div>
    </section>
@endsection
