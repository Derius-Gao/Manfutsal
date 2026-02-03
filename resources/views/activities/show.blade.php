@extends('layouts.app')

@section('title', 'Detail Activity')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-history me-2"></i>Detail Activity</h5>
        <a href="{{ route('activities.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="30%">User</th>
                <td>{{ $activity->user->name ?? '-' }} ({{ $activity->user->email ?? '-' }})</td>
            </tr>
            <tr>
                <th>Action</th>
                <td><span class="badge bg-primary">{{ $activity->action }}</span></td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{ $activity->description }}</td>
            </tr>
            <tr>
                <th>IP Address</th>
                <td>{{ $activity->ip_address }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $activity->created_at->format('d F Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
</div>
@endsection
