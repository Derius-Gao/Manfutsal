@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-calendar-edit me-2"></i>Edit Booking</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal', $booking->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                    <select class="form-select @error('jam_mulai') is-invalid @enderror" id="jam_mulai" name="jam_mulai" required>
                        <option value="">Pilih Jam</option>
                        @for($hour = 6; $hour <= 22; $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ old('jam_mulai', $booking->jam_mulai) == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                {{ sprintf('%02d:00', $hour) }}
                            </option>
                        @endfor
                    </select>
                    @error('jam_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                    <select class="form-select @error('jam_selesai') is-invalid @enderror" id="jam_selesai" name="jam_selesai" required>
                        <option value="">Pilih Jam</option>
                        @for($hour = 7; $hour <= 23; $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ old('jam_selesai', $booking->jam_selesai) == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                {{ sprintf('%02d:00', $hour) }}
                            </option>
                        @endfor
                    </select>
                    @error('jam_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ old('status', $booking->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ old('status', $booking->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif
            
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan', $booking->catatan) }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

