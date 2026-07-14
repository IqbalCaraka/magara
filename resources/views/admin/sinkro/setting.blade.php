@extends('layouts.admin')

@section('title', 'Konfigurasi DMS')

@section('content')
    <div class="page-heading">
        <div class="page-heading-copy">
            <span class="page-icon"><i class="bi bi-gear" aria-hidden="true"></i></span>
            <div>
                <p class="eyebrow mb-1">Sinkronisasi</p>
                <h1 class="h3 mb-1">Konfigurasi Koneksi DMS</h1>
                <p class="text-muted mb-0">Simpan kredensial database DMS secara aman (password terenkripsi).</p>
            </div>
        </div>
        <a href="{{ route('admin.sinkro.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-12 col-lg-7">
            <section class="panel">
                <div class="panel-header">
                    <h2 class="h5 mb-0 section-title"><i class="bi bi-database-gear" aria-hidden="true"></i><span>Koneksi Database DMS</span></h2>
                </div>
                <div class="p-3">
                    <form action="{{ route('admin.sinkro.setting.update') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12 col-sm-8">
                                <label class="form-label" for="host">Host</label>
                                <input type="text" class="form-control @error('host') is-invalid @enderror" id="host" name="host" value="{{ old('host', $config['host']) }}" placeholder="10.100.9.138" required>
                                @error('host') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-sm-4">
                                <label class="form-label" for="port">Port</label>
                                <input type="text" class="form-control @error('port') is-invalid @enderror" id="port" name="port" value="{{ old('port', $config['port'] ?: '3306') }}" required>
                                @error('port') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="database">Nama Database</label>
                                <input type="text" class="form-control @error('database') is-invalid @enderror" id="database" name="database" value="{{ old('database', $config['database']) }}" placeholder="dokumentakah" required>
                                @error('database') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="username">Username</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $config['username']) }}" required>
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ $config['password'] ? '••••••••' : 'Masukkan password' }}">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if($config['password'])
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Simpan Konfigurasi
                            </button>
                            <button type="button" class="btn btn-outline-success" id="btn-test">
                                <i class="bi bi-plug"></i> Test Koneksi
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-5">
            <section class="panel h-100">
                <div class="panel-header">
                    <h2 class="h5 mb-0 section-title"><i class="bi bi-shield-lock" aria-hidden="true"></i><span>Keamanan</span></h2>
                </div>
                <div class="p-3">
                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle"></i> <strong>Password dienkripsi</strong> menggunakan AES-256-CBC (Laravel Crypt) sebelum disimpan ke database. Hanya aplikasi ini yang dapat mendekripsinya menggunakan APP_KEY.
                    </div>
                    <table class="table table-sm table-borderless small mb-0">
                        <tr>
                            <td class="text-muted" style="width:100px">Host</td>
                            <td>{{ $config['host'] ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Port</td>
                            <td>{{ $config['port'] ?: '3306' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Database</td>
                            <td>{{ $config['database'] ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username</td>
                            <td>{{ $config['username'] ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Password</td>
                            <td>{!! $config['password'] ? '<span class="text-success"><i class="bi bi-check-circle"></i> Tersimpan (terenkripsi)</span>' : '<span class="text-danger"><i class="bi bi-x-circle"></i> Belum diset</span>' !!}</td>
                        </tr>
                    </table>

                    <div class="mt-3" id="test-result"></div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.getElementById('btn-test').addEventListener('click', function () {
    const btn = this;
    const result = document.getElementById('test-result');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';
    result.innerHTML = '';

    fetch('{{ route("admin.sinkro.test-connection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            result.innerHTML = '<div class="alert alert-success small py-2 mb-0"><i class="bi bi-check-circle"></i> ' + data.message + '</div>';
        } else {
            result.innerHTML = '<div class="alert alert-danger small py-2 mb-0"><i class="bi bi-x-circle"></i> ' + data.message + '</div>';
        }
    })
    .catch(() => {
        result.innerHTML = '<div class="alert alert-danger small py-2 mb-0"><i class="bi bi-x-circle"></i> Gagal menghubungi server.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plug"></i> Test Koneksi';
    });
});
</script>
@endpush
