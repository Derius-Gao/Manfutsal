@extends('layouts.app')

@section('title', Auth::user()->isCustomer() ? 'Booking Saya' : (Auth::user()->isManager() ? 'Konfirmasi Booking' : 'Semua Booking'))

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>
            <i class="fas fa-calendar-check me-2"></i>
            @if(Auth::user()->isCustomer())
                Booking Saya
            @elseif(Auth::user()->isManager())
                Konfirmasi Booking
            @else
                Semua Booking
            @endif
        </h5>
        <div>
            @if(Auth::user()->isCustomer())
                <a href="{{ route('bookings.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Buat Booking
                </a>
            @endif
            <button class="btn btn-primary" onclick="refreshData()">
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
                        <h3>{{ $pendingCount ?? 0 }}</h3>
                        <small>Menunggu Konfirmasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-check-circle me-2"></i>Confirmed</h5>
                        <h3>{{ $confirmedCount ?? 0 }}</h3>
                        <small>Dikonfirmasi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-times-circle me-2"></i>Rejected</h5>
                        <h3>{{ $rejectedCount ?? 0 }}</h3>
                        <small>Ditolak</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-day me-2"></i>Today</h5>
                        <h3>{{ $todayCount ?? 0 }}</h3>
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
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control" id="filter-tanggal" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <select class="form-select" id="filter-lapangan">
                    <option value="">Semua Lapangan</option>
                    @foreach($lapangans ?? [] as $lapangan)
                        <option value="{{ $lapangan->id }}" {{ request('lapangan_id') == $lapangan->id ? 'selected' : '' }}>{{ $lapangan->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" class="form-control" id="search-booking" placeholder="Cari booking..." value="{{ request('search') }}">
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
                        @if(!Auth::user()->isCustomer())
                            <th>Customer</th>
                        @endif
                        <th>Lapangan</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr class="{{ $booking->status === 'confirmed' ? 'table-success' : ($booking->status === 'pending' ? 'table-warning' : ($booking->status === 'rejected' ? 'table-danger' : '')) }}">
                            <td><span class="badge bg-primary">BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                            @if(!Auth::user()->isCustomer())
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
                            @endif
                            <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                            <td>{{ $booking->tanggal ? $booking->tanggal->format('Y-m-d') : '-' }}</td>
                            <td>
                                @if($booking->jam_mulai && $booking->jam_selesai)
                                    {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $booking->total_harga ? formatRupiah($booking->total_harga) : '-' }}</td>
                            <td>{!! getStatusBadge($booking->status) !!}</td>
                            <td>
                                @if($booking->payment)
                                    <span class="badge bg-{{ $booking->payment->status === 'verified' ? 'success' : ($booking->payment->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($booking->payment->status) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $booking->payment->metode_pembayaran ?? '-')) }}</small>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                    <br>
                                    <small class="text-muted">Belum upload</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="showDetailModal({{ $booking->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(Auth::user()->isManager() || Auth::user()->isAdmin())
                                        @if($booking->payment && $booking->payment->status === 'pending' && $booking->status === 'pending')
                                            <form action="{{ route('payments.verify', $booking->payment->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                <button class="btn btn-success" type="submit" onclick="return confirm('Verifikasi pembayaran dan konfirmasi booking?')" title="Verifikasi Pembayaran">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('payments.reject', $booking->payment->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                <button class="btn btn-danger" type="submit" onclick="return confirm('Tolak pembayaran ini?')" title="Tolak Pembayaran">
                                                    <i class="fas fa-times-circle"></i>
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'pending' && (!$booking->payment || $booking->payment->status === 'verified'))
                                            <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-success" type="submit" onclick="return confirm('Konfirmasi booking ini?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-danger" onclick="rejectBookingClient({{ $booking->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        @if($booking->status === 'confirmed')
                                            <form action="{{ route('bookings.complete', $booking->id) }}" method="POST" style="display:inline-block">
                                                @csrf
                                                @method('PUT')
                                                <button class="btn btn-primary" type="submit" onclick="return confirm('Tandai booking sebagai selesai?')">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                    @if(Auth::user()->isCustomer() && $booking->canBeCancelled())
                                        <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            @method('POST')
                                            <button class="btn btn-warning" type="submit" onclick="return confirm('Batalkan booking ini?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isCustomer() ? '8' : '9' }}" class="text-center">Belum ada booking</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $bookings->links() }}
            </div>
        @endif
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

function showDetailModal(bookingId) {
    fetch(`/bookings/${bookingId}`)
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
                            <tr><td>Catatan</td><td>${booking.catatan || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Informasi Pembayaran</h6>
                        <table class="table table-sm">
                            <tr><td>Status Payment</td><td>${booking.payment ? '<span class="badge bg-' + (booking.payment.status === 'verified' ? 'success' : 'warning') + '">' + booking.payment.status.charAt(0).toUpperCase() + booking.payment.status.slice(1) + '</span>' : '<span class="badge bg-warning">Pending</span>'}</td></tr>
                            <tr><td>Metode</td><td>${booking.payment ? (booking.payment.metode_pembayaran || '-') : '-'}</td></tr>
                            <tr><td>Jumlah</td><td>${booking.payment ? formatRupiah(booking.payment.jumlah) : '-'}</td></tr>
                            ${booking.payment && booking.payment.bukti_pembayaran ? `<tr><td>Bukti</td><td><a href="/storage/${booking.payment.bukti_pembayaran}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> Lihat</a></td></tr>` : ''}
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

function rejectBookingClient(id) {
    Swal.fire({
        title: 'Tolak Booking?',
        input: 'textarea',
        inputLabel: 'Alasan penolakan',
        inputPlaceholder: 'Masukkan alasan penolakan...',
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
            form.action = `/bookings/${id}/reject`;
            
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
    
    const params = new URLSearchParams();
    if (searchTerm) params.append('search', searchTerm);
    if (status) params.append('status', status);
    if (tanggal) params.append('tanggal', tanggal);
    if (lapangan) params.append('lapangan_id', lapangan);
    
    window.location.href = '{{ route("bookings.index") }}?' + params.toString();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('filter-status').addEventListener('change', searchBooking);
    document.getElementById('filter-tanggal').addEventListener('change', searchBooking);
    document.getElementById('filter-lapangan').addEventListener('change', searchBooking);
});
</script>
@endsection

