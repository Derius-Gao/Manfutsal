@extends('layouts.app')

@section('title', 'Log Activity')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-history me-2"></i>Log Activity</h5>
        <div>
            <button class="btn btn-primary" onclick="refreshData()">
                <i class="fas fa-sync me-2"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('activities.index') }}" class="mb-3">
            <div class="row">
                @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin() || Auth::user()->isManager())
                    <div class="col-md-3">
                        <select class="form-select" name="action">
                            <option value="">Semua Activity</option>
                            @foreach($actionList ?? [] as $act)
                                <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $act)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="user">
                            <option value="">Semua User</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-3">
                    <input type="date" class="form-control" name="date" value="{{ request('date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
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
                        <h5><i class="fas fa-list me-2"></i>Total</h5>
                        <h3>{{ $totalActivities }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-sign-in-alt me-2"></i>Login</h5>
                        <h3>{{ $loginActivities }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar me-2"></i>Booking</h5>
                        <h3>{{ $bookingActivities }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-money-bill me-2"></i>Payment</h5>
                        <h3>{{ $paymentActivities }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity List -->
        <div class="timeline">
            @forelse($activities as $activity)
                <div class="timeline-item">
                    <div class="timeline-marker bg-{{ $activity->action === 'login' ? 'info' : ($activity->action === 'booking_confirmed' ? 'success' : ($activity->action === 'booking_rejected' ? 'danger' : 'warning')) }}">
                        <i class="fas fa-{{ $activity->action === 'login' ? 'sign-in-alt' : ($activity->action === 'booking_confirmed' ? 'check-circle' : ($activity->action === 'booking_rejected' ? 'times-circle' : 'calendar')) }}"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</h6>
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
                                    @if($activity->ip_address)
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ $activity->ip_address }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <span class="badge bg-{{ $activity->action === 'login' ? 'info' : ($activity->action === 'booking_confirmed' ? 'success' : ($activity->action === 'booking_rejected' ? 'danger' : 'warning')) }}">
                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada activity</h5>
                </div>
            @endforelse
        </div>

        @if($activities->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
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
@endsection

@section('scripts')
<script>
function refreshData() {
    window.location.reload();
}
</script>
@endsection

