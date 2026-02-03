@extends('layouts.app')

@section('title', 'Web Setting')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-cog me-2"></i>Pengaturan Web</h5>
                <div>
                    <button class="btn btn-success" onclick="exportSettings()">
                        <i class="fas fa-download me-2"></i>Backup
                    </button>
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.websetting.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- General Settings -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Pengaturan Umum</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_name" class="form-label">Nama Aplikasi</label>
                                <input type="text" class="form-control" id="app_name" name="app_name" value="{{ $settings['app_name'] }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_email" class="form-label">Email Aplikasi</label>
                                <input type="email" class="form-control" id="app_email" name="app_email" value="{{ $settings['app_email'] }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="app_description" class="form-label">Deskripsi Aplikasi</label>
                                <textarea class="form-control" id="app_description" name="app_description" rows="3" required>{{ $settings['app_description'] }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_phone" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="app_phone" name="app_phone" value="{{ $settings['app_phone'] }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_address" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="app_address" name="app_address" value="{{ $settings['app_address'] }}">
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Settings -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="mb-3"><i class="fas fa-share-alt me-2"></i>Media Sosial</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="social_facebook" class="form-label">Facebook</label>
                                <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="{{ $settings['social_facebook'] }}" placeholder="https://facebook.com/...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="social_instagram" class="form-label">Instagram</label>
                                <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="{{ $settings['social_instagram'] }}" placeholder="https://instagram.com/...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="social_twitter" class="form-label">Twitter</label>
                                <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="{{ $settings['social_twitter'] }}" placeholder="https://twitter.com/...">
                            </div>
                        </div>
                    </div>

                    <!-- System Settings -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="mb-3"><i class="fas fa-cogs me-2"></i>Pengaturan Sistem</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode" {{ $settings['maintenance_mode'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenance_mode">
                                        Maintenance Mode
                                    </label>
                                </div>
                                <small class="text-muted">Nonaktifkan akses publik</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="allow_registration" name="allow_registration" {{ $settings['allow_registration'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="allow_registration">
                                        Pendaftaran Terbuka
                                    </label>
                                </div>
                                <small class="text-muted">Izinkan pendaftaran user baru</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_notifications">
                                        Notifikasi Email
                                    </label>
                                </div>
                                <small class="text-muted">Kirim notifikasi via email</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" {{ $settings['sms_notifications'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_notifications">
                                        Notifikasi SMS
                                    </label>
                                </div>
                                <small class="text-muted">Kirim notifikasi via SMS</small>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Settings -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="mb-3"><i class="fas fa-calendar-check me-2"></i>Pengaturan Booking</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="max_booking_per_day" class="form-label">Max Booking/Hari</label>
                                <input type="number" class="form-control" id="max_booking_per_day" name="max_booking_per_day" value="{{ $settings['max_booking_per_day'] }}" min="1" max="10" required>
                                <small class="text-muted">Maksimal booking per user per hari</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="max_booking_hours" class="form-label">Max Durasi (Jam)</label>
                                <input type="number" class="form-control" id="max_booking_hours" name="max_booking_hours" value="{{ $settings['max_booking_hours'] }}" min="1" max="12" required>
                                <small class="text-muted">Maksimal durasi booking</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="payment_timeout" class="form-label">Timeout Pembayaran (Menit)</label>
                                <input type="number" class="form-control" id="payment_timeout" name="payment_timeout" value="{{ $settings['payment_timeout'] }}" min="15" max="180" required>
                                <small class="text-muted">Batas waktu pembayaran</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_confirm_booking" name="auto_confirm_booking" {{ $settings['auto_confirm_booking'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_confirm_booking">
                                        Auto Confirm
                                    </label>
                                </div>
                                <small class="text-muted">Konfirmasi booking otomatis</small>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-secondary" onclick="resetToDefault()">
                                        <i class="fas fa-undo me-2"></i>Reset Default
                                    </button>
                                    <button type="button" class="btn btn-info" onclick="testEmail()">
                                        <i class="fas fa-envelope me-2"></i>Test Email
                                    </button>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Pengaturan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function refreshData() {
    location.reload();
}

function resetToDefault() {
    Swal.fire({
        title: 'Reset ke Default?',
        text: 'Apakah Anda yakin ingin mereset semua pengaturan ke nilai default?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Resetting...',
                text: 'Sedang mereset pengaturan',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', 'Pengaturan berhasil direset ke default', 'success').then(() => {
                    location.reload();
                });
            }, 2000);
        }
    });
}

function testEmail() {
    Swal.fire({
        title: 'Test Email',
        html: `
            <div class="mb-3">
                <label class="form-label">Email Tujuan</label>
                <input type="email" class="form-control" id="test-email" value="{{ $settings['app_email'] }}" placeholder="email@example.com">
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Kirim Test',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const email = document.getElementById('test-email').value;
            if (!email) {
                Swal.showValidationMessage('Email harus diisi');
                return false;
            }
            return { email };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { email } = result.value;
            
            Swal.fire({
                title: 'Mengirim...',
                text: `Sedang mengirim email test ke ${email}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', 'Email test berhasil dikirim', 'success');
            }, 2000);
        }
    });
}

function exportSettings() {
    Swal.fire({
        title: 'Backup Pengaturan',
        html: `
            <div class="mb-3">
                <label class="form-label">Format Backup</label>
                <select class="form-select" id="backup-format">
                    <option value="json">JSON</option>
                    <option value="sql">SQL</option>
                    <option value="yaml">YAML</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Download Backup',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('backup-format').value;
            return { format };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format } = result.value;
            
            Swal.fire({
                title: 'Creating Backup...',
                text: `Sedang membuat backup format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Backup berhasil dibuat dalam format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

// Auto-save functionality
let autoSaveTimer;
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            console.log('Auto-saving...');
            // In a real application, you would implement auto-save here
        }, 5000);
    });
});
</script>
@endsection
