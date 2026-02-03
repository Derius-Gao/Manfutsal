@extends('layouts.app')

@section('title', 'Detail Lapangan')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-map me-2"></i>Detail Lapangan</h5>
                <div>
                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                        <a href="{{ route('lapangans.edit', $lapangan->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    @endif
                    <a href="{{ route('lapangans.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($lapangan->foto)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $lapangan->foto) }}" alt="{{ $lapangan->nama }}" class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                    </div>
                @endif
                
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Nama</th>
                        <td>{{ $lapangan->nama }}</td>
                    </tr>
                    <tr>
                        <th>Lokasi</th>
                        <td>{{ $lapangan->lokasi }}</td>
                    </tr>
                    <tr>
                        <th>Daerah</th>
                        <td>{{ $lapangan->daerah }}</td>
                    </tr>
                    <tr>
                        <th>Kapasitas</th>
                        <td>{{ $lapangan->kapasitas }} orang</td>
                    </tr>
                    <tr>
                        <th>Harga per Jam</th>
                        <td>{{ formatRupiah($lapangan->harga_per_jam) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! getStatusBadge($lapangan->status) !!}</td>
                    </tr>
                    <tr>
                        <th>Fasilitas</th>
                        <td>{{ $lapangan->fasilitas ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        @if(Auth::user()->isCustomer())
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-plus me-2"></i>Buat Booking</h5>
                </div>
                <div class="card-body">
                    <p>Ingin booking lapangan ini?</p>
                    <a href="{{ route('bookings.create', ['lapangan_id' => $lapangan->id]) }}" class="btn btn-primary w-100">
                        <i class="fas fa-calendar-check me-2"></i>Booking Sekarang
                    </a>
                </div>
            </div>
        @endif
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Informasi</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Status:</strong> {!! getStatusBadge($lapangan->status) !!}</p>
                <p class="mb-2"><strong>Harga:</strong> {{ formatRupiah($lapangan->harga_per_jam) }}/jam</p>
                <p class="mb-0"><strong>Kapasitas:</strong> {{ $lapangan->kapasitas }} orang</p>
            </div>
        </div>
    </div>
</div>
@endsection

