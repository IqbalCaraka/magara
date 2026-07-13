@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-person-plus" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Management</p>
                <h1 class="h3 mb-1">Tambah User</h1>
                <p class="text-muted mb-0">Buat akun pengguna baru.</p>
            </div>
        </div>
        <div class="heading-actions">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.users.index') }}">
                <i class="bi bi-arrow-left" aria-hidden="true"></i> Kembali
            </a>
        </div>
    </div>

    <section class="panel mt-3">
        <div class="panel-header">
            <h2 class="h5 mb-0 section-title"><i class="bi bi-person-plus" aria-hidden="true"></i><span>Form User</span></h2>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="p-3">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="nama">Nama <span class="text-danger">*</span></label>
                    <input class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" type="text" value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="nip_nik">NIP/NIK</label>
                    <input class="form-control @error('nip_nik') is-invalid @enderror" id="nip_nik" name="nip_nik" type="text" value="{{ old('nip_nik') }}">
                    @error('nip_nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        @foreach(['superadmin', 'admin', 'pic', 'pkl', 'magang', 'viewer'] as $role)
                            <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <div class="alert alert-info small mb-0 py-2">
                        <i class="bi bi-info-circle"></i> Password default: <strong>ditakasnbkn</strong> — user dapat mengubahnya setelah login.
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> Simpan</button>
            </div>
        </form>
    </section>
@endsection
