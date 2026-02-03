@extends('layouts.app')

@section('title', 'Cari Lapangan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-search me-2"></i>Cari Lapangan</h5>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('customer.lapangan.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Cari Nama Lapangan</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Masukkan nama lapangan...">
                        </div>
                        <div class="col-md-3">
                            <label for="daerah" class="form-label">Daerah</label>
                            <select class="form-select" id="daerah" name="daerah">
                                <option value="">Semua Daerah</option>
                                <option value="Jakarta" {{ request('daerah') == 'Jakarta' ? 'selected' : '' }}>Jakarta</option>
                                <option value="Bandung" {{ request('daerah') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                                <option value="Surabaya" {{ request('daerah') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                <option value="Yogyakarta" {{ request('daerah') == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                                <option value="Medan" {{ request('daerah') == 'Medan' ? 'selected' : '' }}>Medan</option>
                                <option value="Semarang" {{ request('daerah') == 'Semarang' ? 'selected' : '' }}>Semarang</option>
                                <option value="Makassar" {{ request('daerah') == 'Makassar' ? 'selected' : '' }}>Makassar</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" 
                                   value="{{ request('tanggal') }}" min="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label><br>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Cari
                            </button>
                            <a href="{{ route('customer.lapangan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Results -->
                <div class="row" id="lapangan-list">
                    <!-- Placeholder untuk data lapangan -->
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6>Lapangan Example</h6>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i> Jakarta, Indonesia
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-users me-1"></i> Kapasitas: 20 orang<br>
                                    <i class="fas fa-money-bill-wave me-1"></i> {{ formatRupiah(100000) }}/jam
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted">Fasilitas:</small><br>
                                    <span class="badge bg-info me-1">Lampu Malam</span>
                                    <span class="badge bg-info me-1">Toilet</span>
                                    <span class="badge bg-info">Parkir</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-success">Aktif</span>
                                    <button class="btn btn-sm btn-primary" onclick="showBookingModal(1)">
                                        <i class="fas fa-calendar-plus me-1"></i>Booking
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="text-center py-5" style="display: none;">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada lapangan ditemukan</h5>
                    <p class="text-muted">Coba ubah filter atau kata kunci pencarian</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bookingForm">
                    @csrf
                    <input type="hidden" id="lapangan_id" name="lapangan_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Lapangan</label>
                        <input type="text" class="form-control" id="lapangan_nama" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="booking_tanggal" class="form-label">Tanggal</label>
                        <input type="date" class="form-control" id="booking_tanggal" name="tanggal" 
                               min="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jam_mulai" class="form-label">Jam Mulai</label>
                            <select class="form-select" id="jam_mulai" name="jam_mulai" required>
                                <option value="">Pilih Jam</option>
                                @for($hour = 6; $hour <= 22; $hour++)
                                    <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jam_selesai" class="form-label">Jam Selesai</label>
                            <select class="form-select" id="jam_selesai" name="jam_selesai" required>
                                <option value="">Pilih Jam</option>
                                @for($hour = 7; $hour <= 23; $hour++)
                                    <option value="{{ sprintf('%02d:00', $hour) }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Total Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" class="form-control" id="total_harga" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitBooking()">
                    <i class="fas fa-save me-2"></i>Simpan Booking
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
// Use real data from Blade template
const lapanganData = @json($lapangans);

function renderLapangan(data) {
    const container = document.getElementById('lapangan-list');
    const emptyState = document.getElementById('empty-state');
    
    if (data.length === 0) {
        container.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    
    container.innerHTML = data.map(lapangan => `
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6>${lapangan.nama}</h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i> ${lapangan.lokasi}
                    </p>
                    <p class="mb-2">
                        <i class="fas fa-users me-1"></i> Kapasitas: ${lapangan.kapasitas} orang<br>
                        <i class="fas fa-money-bill-wave me-1"></i> ${formatRupiah(lapangan.harga_per_jam)}/jam
                    </p>
                    <div class="mb-2">
                        <small class="text-muted">Fasilitas:</small><br>
                        ${lapangan.fasilitas.map(f => `<span class="badge bg-info me-1">${f}</span>`).join('')}
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-success">Aktif</span>
                        <button class="btn btn-sm btn-primary" onclick="showBookingModal(${lapangan.id}, '${lapangan.nama}', ${lapangan.harga_per_jam})">
                            <i class="fas fa-calendar-plus me-1"></i>Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function showBookingModal(id, nama, hargaPerJam) {
    document.getElementById('lapangan_id').value = id;
    document.getElementById('lapangan_nama').value = nama;
    document.getElementById('total_harga').value = '0';
    
    // Store harga per jam for calculation
    document.getElementById('bookingModal').setAttribute('data-harga-per-jam', hargaPerJam);
    
    const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
    modal.show();
}

function calculateTotal() {
    const jamMulai = document.getElementById('jam_mulai').value;
    const jamSelesai = document.getElementById('jam_selesai').value;
    const hargaPerJam = parseInt(document.getElementById('bookingModal').getAttribute('data-harga-per-jam'));
    
    if (jamMulai && jamSelesai && hargaPerJam) {
        const start = new Date(`2000-01-01 ${jamMulai}`);
        const end = new Date(`2000-01-01 ${jamSelesai}`);
        const hours = (end - start) / (1000 * 60 * 60);
        
        if (hours > 0) {
            const total = hours * hargaPerJam;
            document.getElementById('total_harga').value = formatRupiah(total);
        } else {
            document.getElementById('total_harga').value = '0';
        }
    }
}

function submitBooking() {
    const form = document.getElementById('bookingForm');
    const formData = new FormData(form);
    
    // Validation
    if (!formData.get('tanggal') || !formData.get('jam_mulai') || !formData.get('jam_selesai')) {
        Swal.fire('Error', 'Silakan lengkapi semua field yang diperlukan', 'error');
        return;
    }
    
    // Simulate API call (will be replaced with real AJAX)
    Swal.fire({
        title: 'Processing...',
        text: 'Sedang menyimpan booking',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Booking berhasil disimpan', 'success').then(() => {
            bootstrap.Modal.getInstance(document.getElementById('bookingModal')).hide();
            // Redirect to booking list
            window.location.href = '{{ route("customer.booking.index") }}';
        });
    }, 1500);
}

// Event listeners
document.getElementById('jam_mulai').addEventListener('change', calculateTotal);
document.getElementById('jam_selesai').addEventListener('change', calculateTotal);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderLapangan(lapanganData);
});
</script>
@endsection
