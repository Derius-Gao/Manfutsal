@extends('layouts.app')

@section('title', Auth::user()->isCustomer() ? 'Cari Lapangan' : 'Kelola Lapangan')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>
            <i class="fas fa-map me-2"></i>
            @if(Auth::user()->isCustomer())
                Cari Lapangan
            @else
                Kelola Lapangan
            @endif
        </h5>
        @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
            <div>
                <a href="{{ route('lapangans.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Lapangan
                </a>
            </div>
        @endif
    </div>
    <div class="card-body">
        <!-- Filter -->
        <form method="GET" action="{{ route('lapangans.index') }}" class="mb-3">
            <div class="row">
                @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                    <div class="col-md-3">
                        <select class="form-select" name="daerah">
                            <option value="">Semua Daerah</option>
                            @foreach($daerahList ?? [] as $d)
                                <option value="{{ $d }}" {{ request('daerah') == $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ request('status') === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                @endif
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" placeholder="Cari lapangan..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>

        @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-map me-2"></i>Total</h5>
                            <h3>{{ $totalLapangan ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-check-circle me-2"></i>Aktif</h5>
                            <h3>{{ $activeLapangan ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-times-circle me-2"></i>Tidak Aktif</h5>
                            <h3>{{ $inactiveLapangan ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5><i class="fas fa-chart-line me-2"></i>Rata-rata</h5>
                            <h3>{{ $avgPrice ? formatRupiah($avgPrice) : '-' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Lapangan Grid -->
        <div class="row">
            @forelse($lapangans as $lapangan)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        @if($lapangan->foto)
                            <img src="{{ asset('storage/' . $lapangan->foto) }}" class="card-img-top" alt="{{ $lapangan->nama }}" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0">{{ $lapangan->nama }}</h6>
                                <span class="badge bg-{{ $lapangan->status === 'aktif' ? 'success' : 'danger' }}">
                                    {{ $lapangan->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i> {{ $lapangan->lokasi }}
                            </p>
                            <div class="mb-2">
                                <small class="text-muted">Fasilitas:</small><br>
                                @if($lapangan->fasilitas)
                                    @php
                                        $fasilitasArray = explode(',', $lapangan->fasilitas);
                                    @endphp
                                    @foreach(array_slice($fasilitasArray, 0, 3) as $fasilitas)
                                        <span class="badge bg-info me-1">{{ trim($fasilitas) }}</span>
                                    @endforeach
                                    @if(count($fasilitasArray) > 3)
                                        <span class="badge bg-secondary">+{{ count($fasilitasArray) - 3 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>{{ $lapangan->kapasitas }} orang<br>
                                        <i class="fas fa-money-bill-wave me-1"></i>{{ formatRupiah($lapangan->harga_per_jam) }}/jam
                                    </small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-info" onclick="showDetail({{ $lapangan->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(Auth::user()->isAdmin() || Auth::user()->isSuperAdmin())
                                        <a href="{{ route('lapangans.edit', $lapangan->id) }}" class="btn btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('lapangans.toggleStatus', $lapangan->id) }}" method="POST" style="display:inline-block">
                                            @csrf
                                            <button type="submit" class="btn btn-{{ $lapangan->status === 'aktif' ? 'secondary' : 'success' }}" onclick="return confirm('Ubah status lapangan ini?')">
                                                <i class="fas fa-{{ $lapangan->status === 'aktif' ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('lapangans.destroy', $lapangan->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Hapus lapangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @elseif(Auth::user()->isCustomer() && $lapangan->status === 'aktif')
                                        <a href="{{ route('bookings.create') }}?lapangan_id={{ $lapangan->id }}" class="btn btn-primary">
                                            <i class="fas fa-calendar-plus"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-map fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada lapangan</h5>
                    </div>
                </div>
            @endforelse
        </div>

        @if($lapangans->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $lapangans->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Lapangan</h5>
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
@endsection

@section('scripts')
<script>
function showDetail(id) {
    fetch(`/lapangans/${id}`)
        .then(response => response.json())
        .then(data => {
            const lapangan = data.lapangan;
            const fotoUrl = lapangan.foto ? `/storage/${lapangan.foto}` : '/storage/lapangan/default.jpg';
            const fasilitas = lapangan.fasilitas ? lapangan.fasilitas.split(',').map(f => '<span class="badge bg-info me-1">' + f.trim() + '</span>').join('') : '-';
            
            const content = `
                <div class="text-center mb-3">
                    <img src="${fotoUrl}" class="img-fluid rounded" alt="${lapangan.nama}" style="max-height: 200px;" onerror="this.src='/storage/lapangan/default.jpg'">
                </div>
                <table class="table table-sm">
                    <tr><td>Nama</td><td>${lapangan.nama}</td></tr>
                    <tr><td>Lokasi</td><td>${lapangan.lokasi}</td></tr>
                    <tr><td>Daerah</td><td><span class="badge bg-primary">${lapangan.daerah}</span></td></tr>
                    <tr><td>Kapasitas</td><td>${lapangan.kapasitas} orang</td></tr>
                    <tr><td>Harga per Jam</td><td>${formatRupiah(lapangan.harga_per_jam)}</td></tr>
                    <tr><td>Fasilitas</td><td>${fasilitas}</td></tr>
                    <tr><td>Status</td><td>${lapangan.status === 'aktif' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>'}</td></tr>
                </table>
            `;
            
            document.getElementById('detail-content').innerHTML = content;
            new bootstrap.Modal(document.getElementById('detailModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Gagal memuat data lapangan', 'error');
        });
}
</script>
@endsection

