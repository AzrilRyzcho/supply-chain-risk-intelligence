@extends('layouts.admin')

@section('title', 'Admin Panel - Global Watchlist')

@section('content')
<div class="container-fluid py-4">
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-star-fill text-warning me-2"></i>Kelola Daftar Pantauan Global</h4>
                <p class="text-muted small mb-0">Pantau dan kelola seluruh daftar pantauan negara favorit pilihan para pengguna terdaftar.</p>
            </div>
            
            <form action="{{ route('admin.watchlists.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari user, email, negara..." value="{{ $search }}">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID Entri</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Negara Dipantau</th>
                        <th>Kode Negara</th>
                        <th>Ditambahkan Pada</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($watchlists as $wl)
                        <tr>
                            <td>{{ $wl->id }}</td>
                            <td><span class="fw-bold text-slate-800">{{ $wl->user_name }}</span></td>
                            <td>{{ $wl->user_email }}</td>
                            <td><span class="badge bg-primary px-3 py-2 fs-7">{{ $wl->country_name }}</span></td>
                            <td><span class="fw-bold text-slate-700">{{ $wl->country_code }}</span></td>
                            <td>{{ $wl->created_at ? \Carbon\Carbon::parse($wl->created_at)->format('d M Y, H:i') : '-' }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.watchlists.destroy', $wl->id) }}" method="POST" onsubmit="return confirm('Hapus entri watchlist ini dari pengguna terkait?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                        <i class="bi bi-trash me-1"></i>Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">Tidak ada entri watchlist global ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $watchlists->links() }}
        </div>
    </div>
</div>
@endsection
