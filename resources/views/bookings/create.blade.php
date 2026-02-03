@extends('layouts.app')

@section('title', 'Buat Booking')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-calendar-plus me-2"></i>Buat Booking Baru</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('bookings.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="lapangan_id" class="form-label">Lapangan <span class="text-danger">*</span></label>
                    <select class="form-select @error('lapangan_id') is-invalid @enderror" id="lapangan_id" name="lapangan_id" required>
                        <option value="">Pilih Lapangan</option>
                        @foreach($lapangans as $lapangan)
                            <option value="{{ $lapangan->id }}" {{ old('lapangan_id') == $lapangan->id ? 'selected' : '' }}>
                                {{ $lapangan->nama }} - {{ formatRupiah($lapangan->harga_per_jam) }}/jam
                            </option>
                        @endforeach
                    </select>
                    @error('lapangan_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tanggal') is-invalid @enderror" id="tanggal" name="tanggal" value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                    <select class="form-select @error('jam_mulai') is-invalid @enderror" id="jam_mulai" name="jam_mulai" required>
                        <option value="">Pilih Jam</option>
                        @for($hour = 6; $hour <= 22; $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ old('jam_mulai') == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                {{ sprintf('%02d:00', $hour) }}
                            </option>
                        @endfor
                    </select>
                    @error('jam_mulai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                    <select class="form-select @error('jam_selesai') is-invalid @enderror" id="jam_selesai" name="jam_selesai" required>
                        <option value="">Pilih Jam</option>
                        @for($hour = 7; $hour <= 23; $hour++)
                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ old('jam_selesai') == sprintf('%02d:00', $hour) ? 'selected' : '' }}>
                                {{ sprintf('%02d:00', $hour) }}
                            </option>
                        @endfor
                    </select>
                    @error('jam_selesai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                <select class="form-select @error('metode_pembayaran') is-invalid @enderror" id="metode_pembayaran" name="metode_pembayaran" required>
                    <option value="">Pilih Metode</option>
                    <option value="cash" {{ old('metode_pembayaran') == 'cash' ? 'selected' : '' }}>Cash (Bayar di Tempat)</option>
                    <option value="transfer_bank" {{ old('metode_pembayaran') == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                    <option value="ewallet" {{ old('metode_pembayaran') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                </select>
                @error('metode_pembayaran')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan (Opsional)</label>
                <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                @error('catatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

