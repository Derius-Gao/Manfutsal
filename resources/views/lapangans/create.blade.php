@extends('layouts.app')

@section('title', 'Tambah Lapangan')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-map-plus me-2"></i>Tambah Lapangan Baru</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('lapangans.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama" class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                    @error('nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="daerah" class="form-label">Daerah <span class="text-danger">*</span></label>
                    <select class="form-select @error('daerah') is-invalid @enderror" id="daerah" name="daerah" required>
                        <option value="">Pilih Daerah</option>
                        <option value="Jakarta" {{ old('daerah') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                        <option value="Bandung" {{ old('daerah') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                        <option value="Surabaya" {{ old('daerah') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                        <option value="Yogyakarta" {{ old('daerah') == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                        <option value="Medan" {{ old('daerah') == 'Medan' ? 'selected' : '' }}>Medan</option>
                        <option value="Semarang" {{ old('daerah') == 'Semarang' ? 'selected' : '' }}>Semarang</option>
                        <option value="Makassar" {{ old('daerah') == 'Makassar' ? 'selected' : '' }}>Makassar</option>
                    </select>
                    @error('daerah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="lokasi" class="form-label">Lokasi Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('lokasi') is-invalid @enderror" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" required>
                @error('lokasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kapasitas" class="form-label">Kapasitas <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('kapasitas') is-invalid @enderror" id="kapasitas" name="kapasitas" value="{{ old('kapasitas') }}" min="1" required>
                    @error('kapasitas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="harga_per_jam" class="form-label">Harga per Jam <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control @error('harga_per_jam') is-invalid @enderror" id="harga_per_jam" name="harga_per_jam" value="{{ old('harga_per_jam') }}" min="0" required>
                    </div>
                    @error('harga_per_jam')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="fasilitas" class="form-label">Fasilitas</label>
                <textarea class="form-control @error('fasilitas') is-invalid @enderror" id="fasilitas" name="fasilitas" rows="3" placeholder="Contoh: lampu malam, toilet, parkir">{{ old('fasilitas') }}</textarea>
                <small class="text-muted">Pisahkan dengan koma</small>
                @error('fasilitas')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="foto" class="form-label">Foto Lapangan</label>
                <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" name="foto" accept="image/*">
                <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                @error('foto')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('lapangans.index') }}" class="btn btn-secondary">
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

