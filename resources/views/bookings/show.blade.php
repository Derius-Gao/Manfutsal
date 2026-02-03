@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-calendar me-2"></i>Detail Booking</h5>
        <div>
            @if(Auth::user()->isAdmin() || Auth::user()->isManager())
                @if($booking->payment && $booking->payment->status === 'pending' && $booking->status === 'pending')
                    <form action="{{ route('payments.verify', $booking->payment->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        <button type="submit" class="btn btn-success" onclick="return confirm('Verifikasi pembayaran dan konfirmasi booking?')">
                            <i class="fas fa-check-circle me-2"></i>Verifikasi Pembayaran
                        </button>
                    </form>
                    <form action="{{ route('payments.reject', $booking->payment->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak pembayaran ini?')">
                            <i class="fas fa-times-circle me-2"></i>Tolak Pembayaran
                        </button>
                    </form>
                @elseif($booking->status === 'pending' && (!$booking->payment || $booking->payment->status === 'verified'))
                    <form action="{{ route('bookings.confirm', $booking->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Konfirmasi booking ini?')">
                            <i class="fas fa-check me-2"></i>Konfirmasi
                        </button>
                    </form>
                    <button class="btn btn-danger" onclick="rejectBooking()">
                        <i class="fas fa-times me-2"></i>Tolak
                    </button>
                @endif
            @endif
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Informasi Booking</h6>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Kode Booking</th>
                        <td><span class="badge bg-primary">BK{{ str_pad($booking->id, 3, '0', STR_PAD_LEFT) }}</span></td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td>{{ $booking->user->name ?? '-' }} ({{ $booking->user->email ?? '-' }})</td>
                    </tr>
                    <tr>
                        <th>Lapangan</th>
                        <td>{{ $booking->lapangan->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ $booking->tanggal->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jam</th>
                        <td>
                            @if($booking->jam_mulai && $booking->jam_selesai)
                                {{ \Carbon\Carbon::parse($booking->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->jam_selesai)->format('H:i') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td>{{ formatRupiah($booking->total_harga) }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! getStatusBadge($booking->status) !!}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $booking->catatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Informasi Pembayaran</h6>
                @if($booking->payment)
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Status Payment</th>
                            <td>
                                <span class="badge bg-{{ $booking->payment->status === 'verified' ? 'success' : ($booking->payment->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($booking->payment->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Metode</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $booking->payment->metode_pembayaran ?? '-')) }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>{{ formatRupiah($booking->payment->jumlah) }}</td>
                        </tr>
                        @if($booking->payment->bukti_pembayaran)
                            <tr>
                                <th>Bukti Pembayaran</th>
                                <td>
                                    <a href="{{ asset('storage/' . $booking->payment->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye me-1"></i>Lihat
                                    </a>
                                </td>
                            </tr>
                        @elseif(Auth::user()->isCustomer() && $booking->status === 'pending' && $booking->payment->status === 'pending')
                            <tr>
                                <th>Upload Bukti Pembayaran</th>
                                <td>
                                    <form action="{{ route('bookings.uploadPayment', $booking->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="input-group">
                                            <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-upload me-1"></i>Upload
                                            </button>
                                        </div>
                                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    </table>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Belum ada data pembayaran
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function rejectBooking() {
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
            form.action = '{{ route("bookings.reject", $booking->id) }}';
            
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
</script>
@endsection

