@extends('layouts.app')

@section('title', 'Booking Saya')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-calendar me-2"></i>Booking Saya</h5>
                <a href="{{ route('customer.lapangan.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Booking Baru
                </a>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="filter-tanggal" placeholder="Filter Tanggal">
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-booking" placeholder="Cari booking...">
                            <button class="btn btn-outline-secondary" type="button">
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
                                <th>Kode Booking</th>
                                <th>Lapangan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="booking-tbody">
                            @forelse($bookings as $booking)
                                <tr>
                                    <td><span class="badge bg-primary">BK{{ str_pad($booking->id,3,'0',STR_PAD_LEFT) }}</span></td>
                                    <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                                    <td>{{ $booking->tanggal->format('Y-m-d') }}</td>
                                    <td>{{ date('H:i', strtotime($booking->jam_mulai)) }} - {{ date('H:i', strtotime($booking->jam_selesai)) }}</td>
                                    <td>{{ formatRupiah($booking->total_harga) }}</td>
                                    <td>{!! getStatusBadge($booking->status) !!}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailModal" data-booking='@json($booking->loadMissing(["lapangan","payment"]))'>
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($booking->canBeCancelled())
                                            <form method="POST" action="{{ route('customer.booking.cancel', $booking->id) ?? '#' }}" style="display:inline-block">@csrf
                                                <button class="btn btn-sm btn-warning" type="submit"><i class="fas fa-times"></i></button>
                                            </form>
                                        @endif
                                        @if($booking->status==='pending')
                                            <button class="btn btn-sm btn-success" onclick="openUploadModal(@json($booking))"><i class="fas fa-upload"></i></button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Belum ada booking</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="empty-booking" class="text-center py-5" style="display: none;">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada booking</h5>
                    <p class="text-muted">Mulai booking lapangan sekarang</p>
                    <a href="{{ route('customer.lapangan.index') }}" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Cari Lapangan
                    </a>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="payment_booking_id" name="booking_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Booking</label>
                        <input type="text" class="form-control" id="payment_booking_info" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                        <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                            <option value="">Pilih Metode</option>
                            <option value="transfer_bank">Transfer Bank</option>
                            <option value="ewallet">E-Wallet</option>
                            <option value="cash">Tunai</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah Pembayaran</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="jumlah" name="jumlah" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                        <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" 
                               accept="image/*" required>
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="payment_catatan" name="catatan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="uploadSubmit">
                    <i class="fas fa-upload me-2"></i>Upload Bukti
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fill detail modal from data attribute
    document.querySelectorAll('[data-bs-target="#detailModal"]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const data = this.getAttribute('data-booking');
            if (!data) return;
            const booking = JSON.parse(data);

            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Kode Booking:</strong> <span class="badge bg-primary">BK${String(booking.id).padStart(3,'0')}</span></p>
                        <p><strong>Lapangan:</strong> ${booking.lapangan.nama}</p>
                        <p><strong>Tanggal:</strong> ${booking.tanggal}</p>
                        <p><strong>Jam:</strong> ${booking.jam_mulai} - ${booking.jam_selesai}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Total Harga:</strong> ${formatRupiah(booking.total_harga)}</p>
                        <p><strong>Status:</strong> ${getStatusBadgeHtml(booking.status)}</p>
                        <p><strong>Catatan:</strong> ${booking.catatan || '-'}</p>
                    </div>
                </div>
            `;

            document.getElementById('detail-content').innerHTML = content;
        });
    });

    // Upload modal open
    window.openUploadModal = function(booking) {
        document.getElementById('payment_booking_id').value = booking.id;
        document.getElementById('payment_booking_info').value = `BK${String(booking.id).padStart(3,'0')} - ${booking.lapangan.nama}`;
        document.getElementById('jumlah').value = formatRupiah(booking.total_harga);
        // set form action
        document.getElementById('paymentForm').action = `/customer/booking/${booking.id}/upload-payment`;
        new bootstrap.Modal(document.getElementById('paymentModal')).show();
    }

    document.getElementById('uploadSubmit').addEventListener('click', function () {
        const form = document.getElementById('paymentForm');
        const fd = new FormData(form);
        if (!fd.get('metode_pembayaran') || !fd.get('bukti_pembayaran')) {
            Swal.fire('Error', 'Silakan lengkapi semua field yang diperlukan', 'error');
            return;
        }
        fetch(form.action, { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
            .then(r => r.ok ? r.text() : Promise.reject(r))
            .then(() => {
                Swal.fire('Success!', 'Bukti pembayaran berhasil diupload', 'success').then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                    location.reload();
                });
            })
            .catch(() => Swal.fire('Error', 'Gagal mengupload bukti pembayaran', 'error'));
    });
});

// Filter functionality
if (document.getElementById('filter-status')) {
    document.getElementById('filter-status').addEventListener('change', filterBookings);
}
if (document.getElementById('filter-tanggal')) {
    document.getElementById('filter-tanggal').addEventListener('change', filterBookings);
}
if (document.getElementById('search-booking')) {
    document.getElementById('search-booking').addEventListener('input', filterBookings);
}

function filterBookings() {
    // Implement filter logic here (could call server-side API)
    console.log('Filtering bookings...');
}
</script>
@endsection
