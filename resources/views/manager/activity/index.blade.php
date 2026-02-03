@extends('layouts.app')

@section('title', 'Log Activity')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Log Activity</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Log Activity</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-history me-2"></i>Log Activity</h5>
                            <div>
                                <button class="btn btn-success" onclick="exportLog()">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                                <button class="btn btn-primary" onclick="refreshData()">
                                    <i class="fas fa-sync me-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filter -->
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="filter-action" class="form-label">Filter Action</label>
                                    <select class="form-select" id="filter-action">
                                        <option value="">Semua Activity</option>
                                        @foreach($actionList as $act)
                                            <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $act)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-user" class="form-label">Filter User</label>
                                    <select class="form-select" id="filter-user">
                                        <option value="">Semua User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-date" class="form-label">Filter Tanggal</label>
                                    <input type="date" class="form-control" id="filter-date" value="{{ $date ?? now()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="filterActivities()">
                                        <i class="fas fa-filter me-2"></i>Filter
                                    </button>
                                    <button class="btn btn-secondary" onclick="resetFilter()">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Activity Stats -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-list me-2"></i>Total Activity</h5>
                                            <h3 id="total-activity">{{ $totalActivities }}</h3>
                                            <small>Hari ini</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-sign-in-alt me-2"></i>Login Activity</h5>
                                            <h3 id="login-activity">{{ $loginActivities }}</h3>
                                            <small>Total login</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-calendar me-2"></i>Booking Activity</h5>
                                            <h3 id="booking-activity">{{ $bookingActivities }}</h3>
                                            <small>Total booking</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-money-bill me-2"></i>Payment Activity</h5>
                                            <h3 id="payment-activity">{{ $paymentActivities }}</h3>
                                            <small>Total payment</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity Timeline -->
                            <div class="timeline" id="activity-timeline">
                                @forelse($activities as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ getActivityColor($activity->action) }}">
                                            <i class="fas fa-{{ getActivityIcon($activity->action) }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">{{ getActivityTitle($activity->action) }}</h6>
                                                    <p class="text-muted mb-1">{{ $activity->description }}</p>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                                                        </small>
                                                        @if($activity->user)
                                                            <small class="text-muted">
                                                                <i class="fas fa-user me-1"></i>{{ $activity->user->name }}
                                                            </small>
                                                        @endif
n                                                        @if($activity->ip_address)
                                                            <small class="text-muted">
                                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $activity->ip_address }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ getActivityColor($activity->action) }}">{{ ucfirst($activity->action) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">Tidak ada activity</h5>
                                        <p class="text-muted">Tidak ada aktivitas yang cocok dengan filter</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Pagination -->
                            @if($activities->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $activities->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>

@section('scripts')
<script>
function filterActivities() {
    const action = document.getElementById('filter-action').value;
    const user = document.getElementById('filter-user').value;
    const date = document.getElementById('filter-date').value;
    
    const params = new URLSearchParams();
    if (action) params.append('action', action);
    if (user) params.append('user', user);
    if (date) params.append('date', date);
    
    window.location.href = `{{ route('manager.activity.index') }}?${params.toString()}`;
}

function resetFilter() {
    window.location.href = '{{ route('manager.activity.index') }}';
}

function refreshData() {
    location.reload();
}

function exportLog() {
    Swal.fire({
        title: 'Export Activity Log',
        html: `
            <div class="mb-3">
                <label class="form-label">Format Export</label>
                <select class="form-select" id="export-format">
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Date Range</label>
                <div class="row">
                    <div class="col">
                        <input type="date" class="form-control" id="export-start" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" id="export-end" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            const startDate = document.getElementById('export-start').value;
            const endDate = document.getElementById('export-end').value;
            
            return { format, startDate, endDate };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, startDate, endDate } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor activity log ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Activity log berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}
</script>

<?php
// Helper functions for activity display
if (!function_exists('getActivityColor')) {
    function getActivityColor($action) {
        $colors = [
            'login' => 'info',
            'logout' => 'secondary',
            'booking_created' => 'warning',
            'booking_confirmed' => 'success',
            'booking_rejected' => 'danger',
            'booking_completed' => 'primary',
            'payment_uploaded' => 'info',
            'payment_verified' => 'success',
            'user_registered' => 'primary',
            'user_updated' => 'warning',
            'user_deleted' => 'danger',
        ];
        return $colors[$action] ?? 'secondary';
    }
}

if (!function_exists('getActivityIcon')) {
    function getActivityIcon($action) {
        $icons = [
            'login' => 'sign-in-alt',
            'logout' => 'sign-out-alt',
            'booking_created' => 'calendar-plus',
            'booking_confirmed' => 'check-circle',
            'booking_rejected' => 'times-circle',
            'booking_completed' => 'check-double',
            'payment_uploaded' => 'upload',
            'payment_verified' => 'check',
            'user_registered' => 'user-plus',
            'user_updated' => 'user-edit',
            'user_deleted' => 'user-times',
        ];
        return $icons[$action] ?? 'circle';
    }
}

if (!function_exists('getActivityTitle')) {
    function getActivityTitle($action) {
        $titles = [
            'login' => 'Login',
            'logout' => 'Logout',
            'booking_created' => 'Booking Created',
            'booking_confirmed' => 'Booking Confirmed',
            'booking_rejected' => 'Booking Rejected',
            'booking_completed' => 'Booking Completed',
            'payment_uploaded' => 'Payment Uploaded',
            'payment_verified' => 'Payment Verified',
            'user_registered' => 'User Registered',
            'user_updated' => 'User Updated',
            'user_deleted' => 'User Deleted',
        ];
        return $titles[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }
}
?>
@endsection
