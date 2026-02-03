@extends('layouts.app')

@section('title', 'Konfirmasi Booking')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-calendar-check me-2"></i>Konfirmasi Booking</h5>
                <div>
                    <button class="btn btn-success" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-clock me-2"></i>Pending</h5>
                                <h3 id="pending-count">{{ $pendingCount ?? 0 }}</h3>
                                <small>Menunggu Konfirmasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-check-circle me-2"></i>Confirmed</h5>
                                <h3 id="confirmed-count">{{ $confirmedCount ?? 0 }}</h3>
                                <small>Dikonfirmasi</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-times-circle me-2"></i>Rejected</h5>
                                <h3 id="rejected-count">{{ $rejectedCount ?? 0 }}</h3>
                                <small>Ditolak</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-calendar-day me-2"></i>Today</h5>
                                <h3 id="today-count">{{ $todayCount ?? 0 }}</h3>
                                <small>Booking Hari Ini</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="filter-tanggal">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter-lapangan">
                            <option value="">Semua Lapangan</option>
                            @foreach($lapangans ?? [] as $lapangan)
                            <option value="{{ $lapangan->id }}">{{ $lapangan->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-booking" placeholder="Cari booking...">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchBooking()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Booking Table -->
                <div class="table-responsive">
                    <table class="table table-striped">
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
                            @forelse($bookings as $booking)
                                <tr class="{{ $booking->status === 'confirmed' ? 'table-success' : ($booking->status === 'pending' ? 'table-warning' : ($booking->status === 'rejected' ? 'table-danger' : '')) }}">
                                    <td><span class="badge bg-primary">{{ $booking->id ? 'BK' . str_pad($booking->id, 3, '0', STR_PAD_LEFT) : '-' }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($booking->user->name ?? '-', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div>{{ $booking->user->name ?? '-' }}</div>
                                                <small class="text-muted">{{ $booking->user->email ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                                    <td>{{ $booking->tanggal ? \Carbon\Carbon::parse($booking->tanggal)->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        @if($booking->jam_mulai && $booking->jam_selesai)
                                            {{ date('H:i', strtotime($booking->jam_mulai)) }} - {{ date('H:i', strtotime($booking->jam_selesai)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ isset($booking->total_harga) ? 'Rp ' . number_format($booking->total_harga, 0, ',', '.') : '-' }}</td>
                                    <td>{!! $booking->status ? '<span class="badge bg-' . ($booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : ($booking->status === 'rejected' ? 'danger' : 'secondary'))) . '">' . ucfirst($booking->status) . '</span>' : '-' !!}</td>
                                    <td>
                                        @if($booking->payment)
                                            <span class="badge bg-{{ $booking->payment->status === 'paid' ? 'success' : ($booking->payment->status === 'pending' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($booking->payment->status) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $booking->payment->metode_pembayaran ?? '-' }}</small>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                            <br>
                                            <small class="text-muted">Belum upload</small>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="showDetailModal({{ $booking->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('manager.booking.confirm', $booking->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-sm btn-success" type="submit" onclick="return confirm('Konfirmasi booking ini?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-danger" onclick="rejectBookingClient({{ $booking->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        @if($booking->status === 'confirmed')
                                            <form action="{{ route('manager.booking.complete', $booking->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-sm btn-primary" type="submit" onclick="return confirm('Tandai booking sebagai selesai?')">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center">Belum ada booking</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($bookings instanceof \Illuminate\Pagination\LengthAwarePaginator && $bookings->hasPages())
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item {{ $bookings->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $bookings->previousPageUrl() }}" tabindex="{{ $bookings->onFirstPage() ? '-1' : '' }}">Previous</a>
                        </li>
                        
                        @for ($i = 1; $i <= $bookings->lastPage(); $i++)
                            <li class="page-item {{ $bookings->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $bookings->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        
                        <li class="page-item {{ $bookings->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $bookings->nextPageUrl() }}">Next</a>
                        </li>
                    </ul>
                </nav>
                @endif
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

<!-- Payment Proof Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="payment-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="verifyPayment()">
                    <i class="fas fa-check me-2"></i>Verifikasi
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectPayment()">
                    <i class="fas fa-times me-2"></i>Tolak
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function formatRupiah(amount) {
    if (!amount) return '-';
    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function getPaymentBadgeHtml(status) {
    const statusMap = {
        'paid': 'success',
        'pending': 'warning',
        'rejected': 'danger'
    };
    const color = statusMap[status] || 'secondary';
    return `<span class="badge bg-${color}">${status ? status.charAt(0).toUpperCase() + status.slice(1) : '-'}</span>`;
}

function showDetailModal(bookingId) {
    fetch(`/booking/${bookingId}/detail`)
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
                            <tr><td>Status</td><td><span class="badge bg-${booking.status === 'confirmed' ? 'success' : (booking.status === 'pending' ? 'warning' : (booking.status === 'rejected' ? 'danger' : 'secondary'))}">${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pembayaran</h6>
                        <table class="table table-sm">
                            <tr><td>Status Payment</td><td>${booking.payment ? getPaymentBadgeHtml(booking.payment.status) : '<span class="badge bg-warning">Pending</span>'}</td></tr>
                            <tr><td>Metode</td><td>${booking.payment ? booking.payment.metode_pembayaran : '-'}</td></tr>
                            <tr><td>Bukti</td><td>${booking.payment && booking.payment.bukti_pembayaran ? `<button class="btn btn-sm btn-info" onclick="showPaymentProof('${booking.payment.bukti_pembayaran}')"><i class="fas fa-eye"></i> Lihat</button>` : '-'}</td></tr>
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

function showPaymentProof(imagePath) {
    const content = `
        <div class="text-center">
            <img src="/storage/${imagePath}" alt="Bukti Pembayaran" class="img-fluid">
        </div>
    `;
    document.getElementById('payment-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

function rejectBookingClient(id) {
    Swal.fire({
        title: 'Tolak Booking?',
        input: 'textarea',
        inputLabel: 'Alasan penolakan',
        inputPlaceholder: 'Masukkan alasan penolakan...',
        inputAttributes: {
            'aria-label': 'Masukkan alasan penolakan'
        },
        showCancelButton: true,
        confirmButtonText: 'Tolak',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#dc3545',
        preConfirm: (reason) => {
            if (!reason || reason.trim() === '') {
                Swal.showValidationMessage('Alasan penolakan harus diisi');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/booking/${id}/reject`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'PUT';
            
            const reasonField = document.createElement('input');
            reasonField.type = 'hidden';
            reasonField.name = 'reason';
            reasonField.value = result.value;
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            form.appendChild(reasonField);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function refreshData() {
    window.location.reload();
}

function searchBooking() {
    const searchTerm = document.getElementById('search-booking').value;
    const status = document.getElementById('filter-status').value;
    const tanggal = document.getElementById('filter-tanggal').value;
    const lapangan = document.getElementById('filter-lapangan').value;
    
    let url = '{{ url()->current() }}?';
    const params = new URLSearchParams();
    
    if (searchTerm) params.append('search', searchTerm);
    if (status) params.append('status', status);
    if (tanggal) params.append('tanggal', tanggal);
    if (lapangan) params.append('lapangan_id', lapangan);
    
    window.location.href = url + params.toString();
}

// Add event listeners for filter changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('filter-status').addEventListener('change', searchBooking);
    document.getElementById('filter-tanggal').addEventListener('change', searchBooking);
    document.getElementById('filter-lapangan').addEventListener('change', searchBooking);
    
    // Set today's date as default for tanggal filter
    document.getElementById('filter-tanggal').value = new Date().toISOString().split('T')[0];
});
</script>
@endsection