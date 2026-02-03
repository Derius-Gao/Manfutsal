@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-user me-2"></i>Detail User</h5>
        <div>
            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Nama</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>
                            <span class="badge bg-{{ $user->role === 'superadmin' ? 'danger' : ($user->role === 'admin' ? 'warning' : ($user->role === 'manager' ? 'info' : 'primary')) }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $user->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Terdaftar</th>
                        <td>{{ $user->created_at->format('d F Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Statistik</h6>
                <div class="card bg-primary text-white mb-2">
                    <div class="card-body">
                        <h5>Total Booking</h5>
                        <h3>{{ $user->bookings()->count() }}</h3>
                    </div>
                </div>
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total Activity</h5>
                        <h3>{{ $user->activities()->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

