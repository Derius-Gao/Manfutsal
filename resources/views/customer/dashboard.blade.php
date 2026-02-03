@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tachometer-alt me-2"></i>Customer Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-calendar me-2"></i>Booking Saya</h5>
                                <h3>0</h3>
                                <small>Total Booking</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-check-circle me-2"></i>Dikonfirmasi</h5>
                                <h3>0</h3>
                                <small>Booking Aktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-clock me-2"></i>Pending</h5>
                                <h3>0</h3>
                                <small>Menunggu Konfirmasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-history me-2"></i>Riwayat</h5>
                                <h3>0</h3>
                                <small>Selesai</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6>Quick Actions</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('customer.lapangan.index') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-search me-2"></i>Cari Lapangan
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('customer.booking.index') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-calendar me-2"></i>Lihat Booking Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
