@extends('layouts.app')

@section('title', 'Hak Akses')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>
            <i class="fas fa-user-shield me-2"></i>
            Hak Akses / Privilege
        </h5>
        <small class="text-muted">
            Pengaturan level akses berdasarkan role (Customer, Manager, Admin, SuperAdmin).
        </small>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($accessMatrix as $role => $data)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <strong class="text-uppercase">{{ $role }}</strong>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-2">Menu yang bisa diakses:</h6>
                            <ul class="mb-0">
                                @foreach($data['menus'] as $menu)
                                    <li>{{ $menu }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <hr>

        <h5 class="mb-3"><i class="fas fa-users-cog me-2"></i>Daftar User & Role</h5>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-secondary text-uppercase">{{ $user->role }}</span>
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit me-1"></i>Atur Role
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <small class="text-muted">
            Untuk mengubah hak akses, ubah role user (Customer / Manager / Admin / SuperAdmin) melalui menu Kelola User.
        </small>
    </div>
</div>
@endsection


