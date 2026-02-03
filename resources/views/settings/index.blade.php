@extends('layouts.app')

@section('title', 'Web Setting')

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-cog me-2"></i>Pengaturan Sistem</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="app_name" class="form-label">Nama Aplikasi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('app_name') is-invalid @enderror" id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                    @error('app_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="app_email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('app_email') is-invalid @enderror" id="app_email" name="app_email" value="{{ old('app_email', $settings['app_email']) }}" required>
                    @error('app_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="app_description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                <textarea class="form-control @error('app_description') is-invalid @enderror" id="app_description" name="app_description" rows="3" required>{{ old('app_description', $settings['app_description']) }}</textarea>
                @error('app_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="app_phone" class="form-label">No. Telepon</label>
                    <input type="tel" class="form-control @error('app_phone') is-invalid @enderror" id="app_phone" name="app_phone" value="{{ old('app_phone', $settings['app_phone']) }}">
                    @error('app_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="app_address" class="form-label">Alamat</label>
                    <input type="text" class="form-control @error('app_address') is-invalid @enderror" id="app_address" name="app_address" value="{{ old('app_address', $settings['app_address']) }}">
                    @error('app_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <h6 class="mt-4 mb-3">Sosial Media</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="social_facebook" class="form-label">Facebook URL</label>
                    <input type="url" class="form-control @error('social_facebook') is-invalid @enderror" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}">
                    @error('social_facebook')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="social_instagram" class="form-label">Instagram URL</label>
                    <input type="url" class="form-control @error('social_instagram') is-invalid @enderror" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram']) }}">
                    @error('social_instagram')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="social_twitter" class="form-label">Twitter URL</label>
                    <input type="url" class="form-control @error('social_twitter') is-invalid @enderror" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter']) }}">
                    @error('social_twitter')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <h6 class="mt-4 mb-3">Pengaturan Booking</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="max_booking_per_day" class="form-label">Maksimal Booking per Hari <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_booking_per_day') is-invalid @enderror" id="max_booking_per_day" name="max_booking_per_day" value="{{ old('max_booking_per_day', $settings['max_booking_per_day']) }}" min="1" max="10" required>
                    @error('max_booking_per_day')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="max_booking_hours" class="form-label">Maksimal Jam Booking <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('max_booking_hours') is-invalid @enderror" id="max_booking_hours" name="max_booking_hours" value="{{ old('max_booking_hours', $settings['max_booking_hours']) }}" min="1" max="12" required>
                    @error('max_booking_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="payment_timeout" class="form-label">Timeout Pembayaran (menit) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('payment_timeout') is-invalid @enderror" id="payment_timeout" name="payment_timeout" value="{{ old('payment_timeout', $settings['payment_timeout']) }}" min="15" max="180" required>
                    @error('payment_timeout')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <h6 class="mt-4 mb-3">Notifikasi</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" {{ old('email_notifications', $settings['email_notifications']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="email_notifications">
                            Email Notifications
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1" {{ old('sms_notifications', $settings['sms_notifications']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sms_notifications">
                            SMS Notifications
                        </label>
                    </div>
                </div>
            </div>
            
            <h6 class="mt-4 mb-3">Sistem</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="maintenance_mode">
                            Maintenance Mode
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration" value="1" {{ old('allow_registration', $settings['allow_registration']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="allow_registration">
                            Izinkan Registrasi
                        </label>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="auto_confirm_booking" name="auto_confirm_booking" value="1" {{ old('auto_confirm_booking', $settings['auto_confirm_booking']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_confirm_booking">
                            Auto Konfirmasi Booking
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

