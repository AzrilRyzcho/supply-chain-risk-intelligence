@extends('layouts.admin')

@section('title', 'Admin Panel - Kelola User')

@section('content')
<div class="container-fluid py-4">
    <div class="card card-custom p-4 bg-white border border-light-subtle shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-slate-800 mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Pengguna</h4>
                <p class="text-muted small mb-0">Lihat seluruh pengguna yang terdaftar di platform dan kelola hak akses mereka.</p>
            </div>
            
            <form action="{{ route('admin.users.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, email, role..." value="{{ $search }}">
                <button type="submit" class="btn btn-outline-secondary"><i class="bi bi-search"></i></button>
            </form>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Mendaftar Pada</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><span class="fw-bold text-slate-800">{{ $user->name }}</span></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-secondary' }} px-3 py-2 fw-semibold">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('d M Y, H:i') : '-' }}</td>
                            <td class="text-center">
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini? Semua artikel dan datanya akan ikut terhapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger fw-bold">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted small">Sedang Aktif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">Tidak ada user ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
