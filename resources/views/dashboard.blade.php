@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="alert alert-info mb-4">
    <i class="fas fa-user me-2"></i>
    <strong>Anda login sebagai:</strong> {{ Auth::user()->name }} 
    <span class="badge bg-primary ms-2">{{ ucfirst(Auth::user()->role) }}</span>
    <span class="text-muted ms-2">({{ Auth::user()->email }})</span>
</div>

@if(Auth::user()->isCustomer())
    <!-- Customer Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5><i class="fas fa-calendar me-2"></i>Total Booking</h5>
                    <h3>{{ $totalBookings ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5><i class="fas fa-clock me-2"></i>Pending</h5>
                    <h3>{{ $pendingBookings ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5><i class="fas fa-check-circle me-2"></i>Confirmed</h5>
                    <h3>{{ ($totalBookings ?? 0) - ($pendingBookings ?? 0) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5><i class="fas fa-history me-2"></i>Riwayat</h5>
                    <h3>{{ ($totalBookings ?? 0) - ($pendingBookings ?? 0) }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-calendar me-2"></i>Booking Terbaru</h5>
            <a href="{{ route('bookings.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Buat Booking
            </a>
        </div>
        <div class="card-body">
            @if(isset($bookings) && $bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td><span class="badge bg-primary">BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                    <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                                    <td>{{ $booking->tanggal->format('Y-m-d') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}</td>
                                    <td>{{ formatRupiah($booking->total_harga) }}</td>
                                    <td>{!! getStatusBadge($booking->status) !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada booking</h5>
                    <a href="{{ route('bookings.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Buat Booking Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>

@elseif(Auth::user()->isManager())
    <!-- Manager Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5><i class="fas fa-clock me-2"></i>Pending</h5>
                    <h3>{{ $pendingCount ?? 0 }}</h3>
                    <small>Menunggu Konfirmasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5><i class="fas fa-check-circle me-2"></i>Confirmed</h5>
                    <h3>{{ ($todayCount ?? 0) - ($pendingCount ?? 0) }}</h3>
                    <small>Dikonfirmasi</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5><i class="fas fa-calendar-check me-2"></i>Today</h5>
                    <h3>{{ $todayCount ?? 0 }}</h3>
                    <small>Booking Hari Ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5><i class="fas fa-money-bill-wave me-2"></i>Revenue</h5>
                    <h3>{{ formatRupiah(0) }}</h3>
                    <small>Total Pendapatan</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-calendar-check me-2"></i>Booking Terbaru</h5>
        </div>
        <div class="card-body">
            @if(isset($bookings) && $bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Customer</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td><span class="badge bg-primary">BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                    <td>{{ $booking->user->name ?? '-' }}</td>
                                    <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                                    <td>{{ $booking->tanggal->format('Y-m-d') }}</td>
                                    <td>{!! getStatusBadge($booking->status) !!}</td>
                                    <td>
                                        <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada booking</h5>
                </div>
            @endif
        </div>
    </div>

@elseif(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
    <!-- Admin/SuperAdmin Dashboard -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5><i class="fas fa-users me-2"></i>Users</h5>
                    <h3>{{ $totalUsers ?? 0 }}</h3>
                    <small>Total Users</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5><i class="fas fa-map me-2"></i>Lapangan</h5>
                    <h3>{{ $totalLapangans ?? 0 }}</h3>
                    <small>Total Lapangan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5><i class="fas fa-calendar me-2"></i>Bookings</h5>
                    <h3>{{ $totalBookings ?? 0 }}</h3>
                    <small>Total Booking</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5><i class="fas fa-money-bill-wave me-2"></i>Revenue</h5>
                    <h3>{{ formatRupiah($totalIncome ?? 0) }}</h3>
                    <small>Total Pendapatan</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-exclamation-circle me-2"></i>Booking Pending</h5>
                </div>
                <div class="card-body">
                    <h3 class="text-warning">{{ $pendingBookings ?? 0 }}</h3>
                    <p class="text-muted">Menunggu konfirmasi</p>
                    <a href="{{ route('bookings.index') }}?status=pending" class="btn btn-warning">
                        <i class="fas fa-eye me-2"></i>Lihat Semua
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar me-2"></i>Booking Terbaru</h5>
                </div>
                <div class="card-body">
                    @if(isset($recentBookings) && $recentBookings->count() > 0)
                        <div class="list-group">
                            @foreach($recentBookings as $booking)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</strong>
                                            <br>
                                            <small>{{ $booking->user->name ?? '-' }} - {{ $booking->lapangan->nama ?? '-' }}</small>
                                        </div>
                                        <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Belum ada booking</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('users.index') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-users me-2"></i>Kelola User
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('lapangans.index') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-map me-2"></i>Kelola Lapangan
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('bookings.index') }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-calendar me-2"></i>Semua Booking
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('keuangan.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-money-bill-wave me-2"></i>Keuangan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

