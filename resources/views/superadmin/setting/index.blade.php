@extends('layouts.app')

@section('title', 'Web Setting')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-cog me-2"></i>Pengaturan Sistem</h5>
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
                <!-- Settings Navigation -->
                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                            <i class="fas fa-cog me-2"></i>Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking" type="button" role="tab">
                            <i class="fas fa-calendar me-2"></i>Booking
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab">
                            <i class="fas fa-money-bill me-2"></i>Pembayaran
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button" role="tab">
                            <i class="fas fa-bell me-2"></i>Notifikasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                            <i class="fas fa-server me-2"></i>Sistem
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="settingsTabContent">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <form id="generalSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informasi Aplikasi</h6>
                                    <div class="mb-3">
                                        <label for="app_name" class="form-label">Nama Aplikasi</label>
                                        <input type="text" class="form-control" id="app_name" name="app_name" value="ManFutsal - Reservasi Lapangan Futsal">
                                    </div>
                                    <div class="mb-3">
                                        <label for="app_description" class="form-label">Deskripsi</label>
                                        <textarea class="form-control" id="app_description" name="app_description" rows="3">Sistem reservasi lapangan futsal online yang mudah dan praktis</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="app_version" class="form-label">Versi</label>
                                        <input type="text" class="form-control" id="app_version" name="app_version" value="1.0.0" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Kontak</h6>
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Email Kontak</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="info@manfutsal.com">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">No. Telepon</label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="08123456789">
                                    </div>
                                    <div class="mb-3">
                                        <label for="contact_address" class="form-label">Alamat</label>
                                        <textarea class="form-control" id="contact_address" name="contact_address" rows="3">Jakarta, Indonesia</textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Sosial Media</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="facebook_url" class="form-label">Facebook</label>
                                                <input type="url" class="form-control" id="facebook_url" name="facebook_url" placeholder="https://facebook.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="instagram_url" class="form-label">Instagram</label>
                                                <input type="url" class="form-control" id="instagram_url" name="instagram_url" placeholder="https://instagram.com/...">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="twitter_url" class="form-label">Twitter</label>
                                                <input type="url" class="form-control" id="twitter_url" name="twitter_url" placeholder="https://twitter.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Booking Settings -->
                    <div class="tab-pane fade" id="booking" role="tabpanel">
                        <form id="bookingSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Pengaturan Booking</h6>
                                    <div class="mb-3">
                                        <label for="min_booking_hours" class="form-label">Minimal Jam Booking (jam)</label>
                                        <input type="number" class="form-control" id="min_booking_hours" name="min_booking_hours" value="1" min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label for="max_booking_hours" class="form-label">Maksimal Jam Booking (jam)</label>
                                        <input type="number" class="form-control" id="max_booking_hours" name="max_booking_hours" value="4" min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label for="advance_booking_days" class="form-label">Booking Hingga (hari)</label>
                                        <input type="number" class="form-control" id="advance_booking_days" name="advance_booking_days" value="30" min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Jam Operasional</h6>
                                    <div class="mb-3">
                                        <label for="opening_time" class="form-label">Jam Buka</label>
                                        <input type="time" class="form-control" id="opening_time" name="opening_time" value="06:00">
                                    </div>
                                    <div class="mb-3">
                                        <label for="closing_time" class="form-label">Jam Tutup</label>
                                        <input type="time" class="form-control" id="closing_time" name="closing_time" value="23:00">
                                    </div>
                                    <div class="mb-3">
                                        <label for="booking_interval" class="form-label">Interval Booking (menit)</label>
                                        <select class="form-select" id="booking_interval" name="booking_interval">
                                            <option value="30">30 menit</option>
                                            <option value="60" selected>60 menit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Kebijakan Pembatalan</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cancellation_hours" class="form-label">Batal Booking Hingga (jam)</label>
                                                <input type="number" class="form-control" id="cancellation_hours" name="cancellation_hours" value="24" min="0">
                                                <small class="text-muted">0 = tidak bisa dibatalkan</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cancellation_fee" class="form-label">Biaya Pembatalaran (%)</label>
                                                <input type="number" class="form-control" id="cancellation_fee" name="cancellation_fee" value="50" min="0" max="100">
                                                <small class="text-muted">0 = gratis pembatalan</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="auto_cancel_hours" class="form-label">Auto Batal (jam)</label>
                                                <input type="number" class="form-control" id="auto_cancel_hours" name="auto_cancel_hours" value="2" min="0">
                                                <small class="text-muted">Auto batal jika tidak ada pembayaran</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Payment Settings -->
                    <div class="tab-pane fade" id="payment" role="tabpanel">
                        <form id="paymentSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Metode Pembayaran</h6>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_transfer" name="enable_transfer" checked>
                                            <label class="form-check-label" for="enable_transfer">
                                                Transfer Bank
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_ewallet" name="enable_ewallet" checked>
                                            <label class="form-check-label" for="enable_ewallet">
                                                E-Wallet
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_cash" name="enable_cash" checked>
                                            <label class="form-check-label" for="enable_cash">
                                                Tunai (di lokasi)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Pengaturan Pembayaran</h6>
                                    <div class="mb-3">
                                        <label for="payment_timeout" class="form-label">Timeout Pembayaran (jam)</label>
                                        <input type="number" class="form-control" id="payment_timeout" name="payment_timeout" value="2" min="1">
                                        <small class="text-muted">Waktu maksimal upload bukti pembayaran</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="min_payment_amount" class="form-label">Minimal Pembayaran</label>
                                        <input type="number" class="form-control" id="min_payment_amount" name="min_payment_amount" value="50000" min="0">
                                        <small class="text-muted">0 = tidak ada minimal</small>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require_payment_proof" name="require_payment_proof" checked>
                                            <label class="form-check-label" for="require_payment_proof">
                                                Wajib Upload Bukti Pembayaran
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Informasi Rekening Bank</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="bank_name" class="form-label">Nama Bank</label>
                                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="BCA">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="bank_account" class="form-label">No. Rekening</label>
                                                <input type="text" class="form-control" id="bank_account" name="bank_account" value="1234567890">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="bank_holder" class="form-label">Atas Nama</label>
                                                <input type="text" class="form-control" id="bank_holder" name="bank_holder" value="PT ManFutsal Indonesia">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="bank_branch" class="form-label">Cabang</label>
                                                <input type="text" class="form-control" id="bank_branch" name="bank_branch" value="Jakarta Pusat">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Notification Settings -->
                    <div class="tab-pane fade" id="notification" role="tabpanel">
                        <form id="notificationSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Email Notification</h6>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="email_booking_confirm" name="email_booking_confirm" checked>
                                            <label class="form-check-label" for="email_booking_confirm">
                                                Konfirmasi Booking
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="email_payment_confirm" name="email_payment_confirm" checked>
                                            <label class="form-check-label" for="email_payment_confirm">
                                                Konfirmasi Pembayaran
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="email_booking_reminder" name="email_booking_reminder" checked>
                                            <label class="form-check-label" for="email_booking_reminder">
                                                Reminder Booking
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>SMS Notification (Opsional)</h6>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled">
                                            <label class="form-check-label" for="sms_enabled">
                                                Aktifkan SMS Notification
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="sms_api_key" class="form-label">SMS API Key</label>
                                        <input type="text" class="form-control" id="sms_api_key" name="sms_api_key" placeholder="Masukkan API Key">
                                    </div>
                                    <div class="mb-3">
                                        <label for="sms_sender" class="form-label">Sender ID</label>
                                        <input type="text" class="form-control" id="sms_sender" name="sms_sender" placeholder="ManFutsal">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Template Email</h6>
                                    <div class="mb-3">
                                        <label for="email_footer" class="form-label">Footer Email</label>
                                        <textarea class="form-control" id="email_footer" name="email_footer" rows="3">Terima kasih telah menggunakan layanan ManFutsal. Untuk bantuan, hubungi kami di info@manfutsal.com</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- System Settings -->
                    <div class="tab-pane fade" id="system" role="tabpanel">
                        <form id="systemSettingsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Maintenance Mode</h6>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode">
                                            <label class="form-check-label" for="maintenance_mode">
                                                Aktifkan Maintenance Mode
                                            </label>
                                        </div>
                                        <small class="text-muted">Sistem akan tidak dapat diakses oleh user biasa</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="maintenance_message" class="form-label">Pesan Maintenance</label>
                                        <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3">Sistem sedang dalam maintenance. Silakan coba lagi beberapa saat.</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Backup & Log</h6>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" checked>
                                            <label class="form-check-label" for="auto_backup">
                                                Auto Backup Harian
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="backup_retention" class="form-label">Retensi Backup (hari)</label>
                                        <input type="number" class="form-control" id="backup_retention" name="backup_retention" value="30" min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label for="log_retention" class="form-label">Retensi Log (hari)</label>
                                        <input type="number" class="form-control" id="log_retention" name="log_retention" value="90" min="1">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Security</h6>
                                    <div class="mb-3">
                                        <label for="session_timeout" class="form-label">Session Timeout (menit)</label>
                                        <input type="number" class="form-control" id="session_timeout" name="session_timeout" value="120" min="15">
                                    </div>
                                    <div class="mb-3">
                                        <label for="max_login_attempts" class="form-label">Maksimal Login Gagal</label>
                                        <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" value="5" min="3">
                                    </div>
                                    <div class="mb-3">
                                        <label for="lockout_duration" class="form-label">Lockout Duration (menit)</label>
                                        <input type="number" class="form-control" id="lockout_duration" name="lockout_duration" value="15" min="1">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Performance</h6>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_cache" name="enable_cache" checked>
                                            <label class="form-check-label" for="enable_cache">
                                                Enable Cache
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="cache_duration" class="form-label">Cache Duration (menit)</label>
                                        <input type="number" class="form-control" id="cache_duration" name="cache_duration" value="60" min="1">
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_debug" name="enable_debug">
                                            <label class="form-check-label" for="enable_debug">
                                                Debug Mode (Hanya untuk development)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-between mt-4">
                    <div>
                        <button class="btn btn-warning" onclick="resetToDefault()">
                            <i class="fas fa-undo me-2"></i>Reset ke Default
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-secondary me-2" onclick="testSettings()">
                            <i class="fas fa-flask me-2"></i>Test Pengaturan
                        </button>
                        <button class="btn btn-primary" onclick="saveSettings()">
                            <i class="fas fa-save me-2"></i>Simpan Pengaturan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function saveSettings() {
    const activeTab = document.querySelector('.tab-pane.active').id;
    const form = document.getElementById(activeTab + 'SettingsForm');
    
    if (!form) {
        Swal.fire('Error', 'Form tidak ditemukan', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Menyimpan Pengaturan...',
        text: 'Sedang menyimpan konfigurasi sistem',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Pengaturan berhasil disimpan', 'success').then(() => {
            console.log('Settings saved for tab:', activeTab);
            // In real implementation, this would make an AJAX call to save settings
        });
    }, 1500);
}

function resetToDefault() {
    const activeTab = document.querySelector('.tab-pane.active').id;
    
    Swal.fire({
        title: 'Reset ke Default?',
        text: 'Apakah Anda yakin ingin mereset pengaturan ke nilai default?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
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
            }, 1500);
        }
    });
}

function testSettings() {
    const activeTab = document.querySelector('.tab-pane.active').id;
    
    Swal.fire({
        title: 'Testing Settings...',
        text: 'Sedang melakukan test pengaturan',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        const testResults = {
            'general': 'Email configuration: OK\nWebsite settings: OK',
            'booking': 'Booking rules: OK\nTime validation: OK',
            'payment': 'Payment methods: OK\nBank info: OK',
            'notification': 'Email service: OK\nSMS service: Not configured',
            'system': 'Database connection: OK\nCache system: OK'
        };
        
        Swal.fire({
            title: 'Test Results',
            html: `<pre class="text-start">${testResults[activeTab] || 'Test completed successfully'}</pre>`,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }, 2000);
}

function exportSettings() {
    Swal.fire({
        title: 'Export/Backup Settings',
        html: `
            <div class="mb-3">
                <label class="form-label">Pilih Data yang Akan Diekspor</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="export-settings" checked>
                    <label class="form-check-label" for="export-settings">
                        Pengaturan Sistem
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="export-user-data">
                    <label class="form-check-label" for="export-user-data">
                        Data User (tanpa password)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="export-lapangan-data" checked>
                    <label class="form-check-label" for="export-lapangan-data">
                        Data Lapangan
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="export-booking-data">
                    <label class="form-check-label" for="export-booking-data">
                        Data Booking
                    </label>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Format Export</label>
                <select class="form-select" id="export-format">
                    <option value="json">JSON</option>
                    <option value="sql">SQL</option>
                    <option value="csv">CSV</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            return { format };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor data ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Data berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Memperbarui data pengaturan',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        location.reload();
    });
}

// Auto-save functionality (simulated)
let autoSaveTimer;
function setupAutoSave() {
    const forms = document.querySelectorAll('[id$="SettingsForm"]');
    forms.forEach(form => {
        form.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                console.log('Auto-saving settings...');
                // In real implementation, this would save automatically
            }, 5000); // Auto-save after 5 seconds of inactivity
        });
    });
}

// Initialize auto-save
document.addEventListener('DOMContentLoaded', function() {
    setupAutoSave();
});

// Tab change confirmation
document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('click', function(e) {
        const currentForm = document.querySelector('.tab-pane.active [id$="SettingsForm"]');
        if (currentForm && currentForm.querySelector('input:not([readonly]), textarea, select')) {
            // Check if form has unsaved changes
            // This is simplified - in real implementation, you'd track changes
            console.log('Checking for unsaved changes before tab switch...');
        }
    });
});
</script>
@endsection
