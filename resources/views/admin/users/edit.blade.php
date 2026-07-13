@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Management</p>
                <h1 class="h3 mb-1">Edit User</h1>
                <p class="text-muted mb-0">Ubah data pengguna {{ $user->nama }}.</p>
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
            <h2 class="h5 mb-0 section-title"><i class="bi bi-pencil" aria-hidden="true"></i><span>Form Edit User</span></h2>
        </div>
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-3">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="nama">Nama <span class="text-danger">*</span></label>
                    <input class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" type="text" value="{{ old('nama', $user->nama) }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="nip_nik">NIP/NIK</label>
                    <input class="form-control @error('nip_nik') is-invalid @enderror" id="nip_nik" name="nip_nik" type="text" value="{{ old('nip_nik', $user->nip_nik) }}">
                    @error('nip_nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        @foreach(['superadmin', 'admin', 'pic', 'pkl', 'magang', 'viewer'] as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                    <input class="form-control" id="password_confirmation" name="password_confirmation" type="password">
                </div>
            </div>
            <div class="mt-4">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </form>
    </section>
@endsection
