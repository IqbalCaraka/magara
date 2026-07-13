@extends('layouts.admin')

@section('title', 'Manajemen User')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Management</p>
                <h1 class="h3 mb-1">Manajemen User</h1>
                <p class="text-muted mb-0">Kelola akun pengguna, role, dan status.</p>
            </div>
        </div>
        <div class="heading-actions">
            <a class="btn btn-primary btn-sm" href="{{ route('admin.users.create') }}">
                <i class="bi bi-person-plus" aria-hidden="true"></i> Tambah User
            </a>
        </div>
    </div>

    <section class="row g-3 mt-1" aria-label="User summary">
        <div class="col-12 col-sm-6 col-xl-3">
            <article class="metric-card metric-primary">
                <div class="metric-top">
                    <span class="metric-label">Total Users</span>
                    <span class="metric-icon"><i class="bi bi-people" aria-hidden="true"></i></span>
                </div>
                <div class="metric-value">{{ $users->total() }}</div>
            </article>
        </div>
    </section>

    <section class="panel mt-3">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span>Daftar User</span></h2>
                <p class="text-muted mb-0">Semua pengguna yang terdaftar di sistem.</p>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="usersTable" data-searchable-table>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">NIP/NIK</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Dibuat</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img class="avatar-img avatar-sm" src="{{ asset('assets/images/avatar/avatar.jpg') }}" alt="{{ $user->nama }}">
                                    <div>
                                        <p class="fw-semibold mb-0">{{ $user->nama }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->nip_nik ?? '-' }}</td>
                            <td>{{ $user->email ?? '-' }}</td>
                            <td><span class="badge text-bg-{{ $user->role === 'superadmin' ? 'danger' : ($user->role === 'admin' ? 'primary' : ($user->role === 'pic' ? 'warning' : 'success')) }}">{{ ucfirst($user->role) }}</span></td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Reset password {{ $user->nama }} ke default (ditakasnbkn)?')">
                                    @csrf
                                    <button class="btn btn-light btn-sm text-warning" type="submit" title="Reset Password"><i class="bi bi-key"></i></button>
                                </form>
                                <a class="btn btn-light btn-sm" href="{{ route('admin.users.edit', $user) }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-light btn-sm text-danger" type="submit" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $users->links() }}
            </div>
        @endif
    </section>
@endsection
