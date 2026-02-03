@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tachometer-alt me-2"></i>Manager Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-clock me-2"></i>Pending</h5>
                                <h3>0</h3>
                                <small>Booking Menunggu Konfirmasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-check-circle me-2"></i>Confirmed</h5>
                                <h3>0</h3>
                                <small>Booking Dikonfirmasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-calendar-check me-2"></i>Today</h5>
                                <h3>0</h3>
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
                
                <div class="mt-4">
                    <h6>Quick Actions</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('manager.booking.index') }}" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-calendar-check me-2"></i>Konfirmasi Booking
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('manager.keuangan.index') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-money-bill-wave me-2"></i>Lihat Keuangan
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('manager.activity.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-history me-2"></i>Log Activity
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
