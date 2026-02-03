@extends('layouts.app')

@section('title', 'Kelola User')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-users me-2"></i>Kelola User</h5>
        <div>
            <a href="{{ route('users.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Tambah User
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('users.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-select" name="role">
                        <option value="">Semua Role</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Cari user..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-users me-2"></i>Total User</h5>
                        <h3>{{ $totalUsers }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-user me-2"></i>Customer</h5>
                        <h3>{{ $customerCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-user-tie me-2"></i>Staff</h5>
                        <h3>{{ $staffCount }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-user-check me-2"></i>Aktif</h5>
                        <h3>{{ $activeCount }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role === 'superadmin' ? 'danger' : ($user->role === 'admin' ? 'warning' : ($user->role === 'manager' ? 'info' : 'primary')) }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(Auth::user()->isSuperAdmin())
                                        <form action="{{ route('users.updateRole', $user->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Ubah role user ini?')">
                                            @csrf
                                            <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>Customer</option>
                                                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="superadmin" {{ $user->role === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.toggleStatus', $user->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }}" onclick="return confirm('Ubah status user ini?')">
                                                <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data user</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

