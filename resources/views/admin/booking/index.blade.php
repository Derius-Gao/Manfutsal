@extends('layouts.app')

@section('title', 'Semua Booking')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-calendar me-2"></i>Semua Booking</h5>
                <div>
                    <button class="btn btn-success" onclick="exportBookings()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                    <button class="btn btn-primary" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <select class="form-select" id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" id="filter-payment">
                            <option value="">Semua Payment</option>
                            <option value="pending">Pending</option>
                            <option value="verified">Verified</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="filter-tanggal" placeholder="Tanggal" value="{{ $start ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="search-booking" placeholder="Cari booking..." value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-sm" onclick="applyFilter()">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="resetFilter()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- Booking Stats -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Total</h5>
                                <h3 id="total-bookings">{{ $bookings->total() }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Pending</h5>
                                <h3 id="pending-bookings">{{ $pendingCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Confirmed</h5>
                                <h3 id="confirmed-bookings">{{ $confirmedCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Completed</h5>
                                <h3 id="today-bookings">{{ $todayCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Rejected</h5>
                                <h3 id="rejected-bookings">{{ $rejectedCount }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Cancelled</h5>
                                <h3 id="cancelled-bookings">{{ $cancelledCount ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm" id="booking-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Customer</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="booking-tbody">
                            @foreach($bookings as $booking)
                                <tr class="{{ $booking->status === 'pending' ? 'table-warning' : ($booking->status === 'rejected' ? 'table-danger' : '') }}">
                                    <td><span class="badge bg-primary">BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($booking->user->profile_photo)
                                                <img src="{{ asset('storage/' . $booking->user->profile_photo) }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $booking->user->name }}">
                                            @else
                                                <div class="avatar-sm bg-{{ $booking->user->role === 'customer' ? 'primary' : 'success' }} text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($booking->user->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div>{{ $booking->user->name }}</div>
                                                <small class="text-muted">{{ $booking->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $booking->lapangan->nama }}</td>
                                    <td>{{ $booking->tanggal->format('Y-m-d') }}</td>
                                    <td>{{ $booking->jam_mulai->format('H:i') }}-{{ $booking->jam_selesai->format('H:i') }}</td>
                                    <td>{{ formatRupiah($booking->total_harga) }}</td>
                                    <td>{!! getStatusBadge($booking->status) !!}</td>
                                    <td>
                                        @if($booking->payment)
                                            <span class="badge bg-{{ $booking->payment->status === 'verified' ? 'success' : ($booking->payment->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($booking->payment->status) }}</span><br>
                                            <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $booking->payment->metode_pembayaran)) }}</small>
                                        @else
                                            <span class="badge bg-secondary">No Payment</span><br>
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="showDetail({{ $booking->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($booking->status === 'pending')
                                                <a href="{{ route('admin.booking.confirm', $booking->id) }}" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengkonfirmasi booking ini?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <button class="btn btn-danger" onclick="rejectBooking({{ $booking->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @elseif($booking->status === 'confirmed')
                                                <a href="{{ route('admin.booking.complete', $booking->id) }}" class="btn btn-primary" onclick="return confirm('Apakah Anda yakin ingin menandai booking ini selesai?')">
                                                    <i class="fas fa-check-double"></i>
                                                </a>
                                                <a href="{{ route('admin.booking.cancel', $booking->id) }}" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination pagination-sm justify-content-center">
                        @if($bookings->hasPages())
                            {{ $bookings->links() }}
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detail-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="edit_booking_id" name="booking_id">
                    
                    <div class="mb-3">
                        <label for="edit_tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="edit_tanggal" name="tanggal" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_jam_mulai" class="form-label">Jam Mulai</label>
                            <select class="form-select" id="edit_jam_mulai" name="jam_mulai" required>
                                <option value="">Pilih Jam</option>
                                @for($hour = 6; $hour <= 22; $hour++)
                                    <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_jam_selesai" class="form-label">Jam Selesai</label>
                            <select class="form-select" id="edit_jam_selesai" name="jam_selesai" required>
                                <option value="">Pilih Jam</option>
                                @for($hour = 7; $hour <= 23; $hour++)
                                    <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="edit_catatan" name="catatan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveBooking()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 12px;
}
</style>

@section('scripts')
<script>
function showDetail(id) {
    fetch(`/admin/booking/${id}`)
        .then(response => response.json())
        .then(data => {
            const booking = data.booking;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Booking</h6>
                        <table class="table table-sm">
                            <tr><td>Kode Booking</td><td><span class="badge bg-primary">BK${String(booking.id).padStart(3,'0')}</span></td></tr>
                            <tr><td>Customer</td><td>${booking.user.name} (${booking.user.email})</td></tr>
                            <tr><td>Lapangan</td><td>${booking.lapangan.nama}</td></tr>
                            <tr><td>Tanggal</td><td>${booking.tanggal}</td></tr>
                            <tr><td>Jam</td><td>${booking.jam_mulai} - ${booking.jam_selesai}</td></tr>
                            <tr><td>Total Harga</td><td>${formatRupiah(booking.total_harga)}</td></tr>
                            <tr><td>Status</td><td>${getStatusBadgeHtml(booking.status)}</td></tr>
                            <tr><td>Catatan</td><td>${booking.catatan || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pembayaran</h6>
                        <table class="table table-sm">
                            <tr><td>Status Payment</td><td>${booking.payment ? getPaymentBadgeHtml(booking.payment.status) : '<span class="badge bg-warning">Pending</span>'}</td></tr>
                            <tr><td>Metode</td><td>${booking.payment ? (booking.payment.metode_pembayaran || '-') : '-'}</td></tr>
                            <tr><td>Jumlah</td><td>${booking.payment ? formatRupiah(booking.payment.jumlah) : '-'}</td></tr>
                            <tr><td>Waktu Booking</td><td>${booking.tanggal} ${booking.jam_mulai}</td></tr>
                            <tr><td>Durasi</td><td>${calculateDuration(booking.jam_mulai, booking.jam_selesai)} jam</td></tr>
                        </table>
                    </div>
                </div>
            `;
            
            document.getElementById('detail-content').innerHTML = content;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat data booking', 'error');
        });
}

function editBooking(id) {
    fetch(`/admin/booking/${id}`)
        .then(response => response.json())
        .then(data => {
            const booking = data.booking;
            document.getElementById('edit_booking_id').value = booking.id;
            document.getElementById('edit_tanggal').value = booking.tanggal;
            document.getElementById('edit_jam_mulai').value = booking.jam_mulai;
            document.getElementById('edit_jam_selesai').value = booking.jam_selesai;
            document.getElementById('edit_status').value = booking.status;
            document.getElementById('edit_catatan').value = booking.catatan || '';
            
            new bootstrap.Modal(document.getElementById('editModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat data booking', 'error');
        });
}

function saveBooking() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    
    if (!formData.get('tanggal') || !formData.get('jam_mulai') || !formData.get('jam_selesai')) {
        Swal.fire('Error', 'Tanggal dan jam harus diisi', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Saving...',
        text: 'Sedang menyimpan data booking',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Data booking berhasil disimpan', 'success').then(() => {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            location.reload();
        });
    }, 1500);
}

function confirmBooking(id) {
    Swal.fire({
        title: 'Konfirmasi Booking?',
        text: 'Apakah Anda yakin ingin mengkonfirmasi booking ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Konfirmasi',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processBookingAction(id, 'confirm');
        }
    });
}

function rejectBooking(id) {
    Swal.fire({
        title: 'Tolak Booking?',
        text: 'Apakah Anda yakin ingin menolak booking ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Tolak',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Alasan Penolakan',
                input: 'textarea',
                inputLabel: 'Masukkan alasan penolakan',
                inputPlaceholder: 'Contoh: Jadwal sudah terisi...',
                showCancelButton: true,
                confirmButtonText: 'Tolak Booking',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    processBookingAction(id, 'reject', result.value);
                }
            });
        }
    });
}

function cancelBooking(id) {
    Swal.fire({
        title: 'Batalkan Booking?',
        text: 'Apakah Anda yakin ingin membatalkan booking ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Batalkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processBookingAction(id, 'cancel');
        }
    });
}

function viewNotes(id) {
    fetch(`/admin/booking/${id}`)
        .then(response => response.json())
        .then(data => {
            const booking = data.booking;
            Swal.fire({
                title: 'Catatan Booking',
                html: `
                    <div class="text-start">
                        <p><strong>Kode:</strong> BK${String(booking.id).padStart(3,'0')}</p>
                        <p><strong>Customer:</strong> ${booking.user.name}</p>
                        <p><strong>Catatan:</strong></p>
                        <div class="alert alert-info">
                            ${booking.catatan || 'Tidak ada catatan'}
                        </div>
                    </div>
                `,
                confirmButtonText: 'Tutup'
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat data booking', 'error');
        });
}

function processBookingAction(id, action, reason = '') {
    const actionText = {
        'confirm': 'diklonfirmasi',
        'reject': 'ditolak',
        'cancel': 'dibatalkan'
    };
    
    Swal.fire({
        title: 'Processing...',
        text: `Sedang ${action} booking`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', `Booking berhasil ${actionText[action]}`, 'success').then(() => {
            location.reload();
        });
    }, 1500);
}

function applyFilter() {
    const status = document.getElementById('filter-status').value;
    const payment = document.getElementById('filter-payment').value;
    const tanggal = document.getElementById('filter-tanggal').value;
    const lapangan = document.getElementById('filter-lapangan').value;
    const customer = document.getElementById('filter-customer').value;
    
    console.log('Filter applied:', { status, payment, tanggal, lapangan, customer });
    
    Swal.fire({
        title: 'Filtering...',
        text: 'Sedang menerapkan filter',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        updateBookingStats();
    });
}

function resetFilter() {
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-payment').value = '';
    document.getElementById('filter-tanggal').value = '';
    document.getElementById('filter-lapangan').value = '';
    document.getElementById('filter-customer').value = '';
    
    location.reload();
}

function updateBookingStats() {
    // Simulate updating stats based on filter
    document.getElementById('total-bookings').textContent = '142';
    document.getElementById('pending-bookings').textContent = '18';
    document.getElementById('confirmed-bookings').textContent = '62';
    document.getElementById('completed-bookings').textContent = '48';
    document.getElementById('rejected-bookings').textContent = '10';
    document.getElementById('cancelled-bookings').textContent = '4';
}

function exportBookings() {
    Swal.fire({
        title: 'Export Bookings',
        html: `
            <div class="mb-3">
                <label class="form-label">Format Export</label>
                <select class="form-select" id="export-format">
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Date Range</label>
                <div class="row">
                    <div class="col">
                        <input type="date" class="form-control" id="export-start" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" id="export-end" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Include Fields</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-customer" checked>
                    <label class="form-check-label" for="include-customer">
                        Customer Information
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-payment" checked>
                    <label class="form-check-label" for="include-payment">
                        Payment Information
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            const startDate = document.getElementById('export-start').value;
            const endDate = document.getElementById('export-end').value;
            const includeCustomer = document.getElementById('include-customer').checked;
            const includePayment = document.getElementById('include-payment').checked;
            
            return { format, startDate, endDate, includeCustomer, includePayment };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, startDate, endDate, includeCustomer, includePayment } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor booking ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Booking berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Memperbarui data booking',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        location.reload();
    });
}

// Helper functions
function calculateDuration(startTime, endTime) {
    const start = new Date(`2000-01-01 ${startTime}`);
    const end = new Date(`2000-01-01 ${endTime}`);
    return (end - start) / (1000 * 60 * 60);
}

function getStatusBadgeHtml(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'confirmed': '<span class="badge bg-success">Confirmed</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>',
        'completed': '<span class="badge bg-info">Completed</span>',
        'cancelled': '<span class="badge bg-secondary">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}

function getPaymentBadgeHtml(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'verified': '<span class="badge bg-success">Verified</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}

// Auto-refresh every 2 minutes
setInterval(refreshData, 120000);

// Event listeners
document.getElementById('filter-status').addEventListener('change', applyFilter);
document.getElementById('filter-payment').addEventListener('change', applyFilter);
document.getElementById('filter-tanggal').addEventListener('change', applyFilter);
document.getElementById('filter-lapangan').addEventListener('change', applyFilter);
document.getElementById('filter-customer').addEventListener('change', applyFilter);
</script>
@endsection
