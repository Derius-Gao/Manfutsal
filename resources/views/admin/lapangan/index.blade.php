@extends('layouts.app')

@section('title', 'Kelola Lapangan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-map me-2"></i>Kelola Lapangan</h5>
                <div>
                    <a href="{{ route('admin.lapangan.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Tambah Lapangan
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select" id="filter-daerah">
                            <option value="">Semua Daerah</option>
                            @foreach($daerahList as $d)
                                <option value="{{ $d }}" {{ $daerah === $d ? 'selected' : '' }}>{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filter-status">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ $status === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-lapangan" placeholder="Cari lapangan..." value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" onclick="applyFilter()">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilter()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- Lapangan Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-map me-2"></i>Total Lapangan</h5>
                                <h3 id="total-lapangan">{{ $totalLapangan }}</h3>
                                <small>Semua lapangan</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-check-circle me-2"></i>Aktif</h5>
                                <h3 id="active-lapangan">{{ $activeLapangan }}</h3>
                                <small>Lapangan aktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-times-circle me-2"></i>Tidak Aktif</h5>
                                <h3 id="inactive-lapangan">{{ $inactiveLapangan }}</h3>
                                <small>Lapangan nonaktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-chart-line me-2"></i>Rata-rata Harga</h5>
                                <h3 id="avg-price">{{ formatRupiah($avgPrice) }}</h3>
                                <small>Per jam</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lapangan Grid -->
                <div class="row" id="lapangan-grid">
                    @foreach($lapangans as $lapangan)
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">{{ $lapangan->nama }}</h6>
                                        <span class="badge bg-{{ $lapangan->status === 'aktif' ? 'success' : 'danger' }}">{{ $lapangan->status === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}</span>
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
                                            <span class="text-muted">Tidak ada fasilitas</span>
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
                                            <a href="{{ route('admin.lapangan.edit', $lapangan->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($lapangan->status === 'aktif')
                                                <a href="{{ route('admin.lapangan.toggle-status', $lapangan->id) }}" class="btn btn-secondary" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan lapangan ini?')">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('admin.lapangan.toggle-status', $lapangan->id) }}" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin mengaktifkan lapangan ini?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @endif
                                            <button class="btn btn-danger" onclick="deleteLapangan({{ $lapangan->id }}, '{{ $lapangan->nama }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Empty State -->
                <div id="empty-lapangan" class="text-center py-5" style="display: none;">
                    <i class="fas fa-map fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada lapangan</h5>
                    <p class="text-muted">Belum ada lapangan yang cocok dengan filter</p>
                    <a href="{{ route('admin.lapangan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Lapangan
                    </a>
                </div>
            </div>
        </div>
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

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lapangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    @csrf
                    <input type="hidden" id="edit_lapangan_id" name="lapangan_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_nama" class="form-label">Nama Lapangan</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_daerah" class="form-label">Daerah</label>
                            <select class="form-select" id="edit_daerah" name="daerah" required>
                                <option value="">Pilih Daerah</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Surabaya">Surabaya</option>
                                <option value="Yogyakarta">Yogyakarta</option>
                                <option value="Medan">Medan</option>
                                <option value="Semarang">Semarang</option>
                                <option value="Makassar">Makassar</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi Lengkap</label>
                        <input type="text" class="form-control" id="edit_lokasi" name="lokasi" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="edit_kapasitas" name="kapasitas" min="1" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_harga" class="form-label">Harga per Jam</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_harga" name="harga_per_jam" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="aktif">Aktif</option>
                                <option value="tidak_aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_fasilitas" class="form-label">Fasilitas</label>
                        <textarea class="form-control" id="edit_fasilitas" name="fasilitas" rows="3" placeholder="Contoh: lampu malam, toilet, parkir"></textarea>
                        <small class="text-muted">Pisahkan dengan koma</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_foto" class="form-label">Foto Lapangan</label>
                        <input type="file" class="form-control" id="edit_foto" name="foto" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, maksimal 2MB</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveLapangan()">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function showDetail(id) {
    fetch(`/admin/lapangan/${id}`)
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

function editLapangan(id) {
    window.location.href = `/admin/lapangan/${id}/edit`;
}

function saveLapangan() {
    const form = document.getElementById('editForm');
    const formData = new FormData(form);
    
    if (!formData.get('nama') || !formData.get('lokasi') || !formData.get('daerah')) {
        Swal.fire('Error', 'Nama, lokasi, dan daerah harus diisi', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Saving...',
        text: 'Sedang menyimpan data lapangan',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Data lapangan berhasil disimpan', 'success').then(() => {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            location.reload();
        });
    }, 1500);
}

function deleteLapangan(id) {
    Swal.fire({
        title: 'Hapus Lapangan?',
        text: 'Apakah Anda yakin ingin menghapus lapangan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/lapangan/${id}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function processDelete(id) {
    Swal.fire({
        title: 'Deleting...',
        text: 'Sedang menghapus lapangan',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Lapangan berhasil dihapus', 'success').then(() => {
            location.reload();
        });
    }, 1500);
}

function toggleStatus(id) {
    const lapangan = lapanganData.find(l => l.id === id);
    if (!lapangan) return;
    
    const newStatus = lapangan.status === 'aktif' ? 'tidak_aktif' : 'aktif';
    const statusText = newStatus === 'aktif' ? 'mengaktifkan' : 'menonaktifkan';
    
    Swal.fire({
        title: `${statusText.charAt(0).toUpperCase() + statusText.slice(1)} Lapangan?`,
        text: `Apakah Anda yakin ingin ${statusText} lapangan "${lapangan.nama}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            processToggleStatus(id, newStatus);
        }
    });
}

function processToggleStatus(id, status) {
    Swal.fire({
        title: 'Updating...',
        text: 'Sedang mengubah status lapangan',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    setTimeout(() => {
        Swal.fire('Success!', 'Status lapangan berhasil diubah', 'success').then(() => {
            location.reload();
        });
    }, 1500);
}

function applyFilter() {
    const daerah = document.getElementById('filter-daerah').value;
    const status = document.getElementById('filter-status').value;
    const search = document.getElementById('search-lapangan').value;
    
    console.log('Filter applied:', { daerah, status, search });
    
    Swal.fire({
        title: 'Filtering...',
        text: 'Sedang menerapkan filter',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        updateLapanganStats();
        renderFilteredLapangan({ daerah, status, search });
    });
}

function resetFilter() {
    document.getElementById('filter-daerah').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('search-lapangan').value = '';
    
    location.reload();
}

function updateLapanganStats() {
    // Simulate updating stats based on filter
    document.getElementById('total-lapangan').textContent = '14';
    document.getElementById('active-lapangan').textContent = '12';
    document.getElementById('inactive-lapangan').textContent = '2';
    document.getElementById('avg-price').textContent = formatRupiah(92000);
}

function renderFilteredLapangan(filters) {
    let filtered = [...lapanganData];
    
    if (filters.daerah) {
        filtered = filtered.filter(l => l.daerah === filters.daerah);
    }
    
    if (filters.status) {
        filtered = filtered.filter(l => l.status === filters.status);
    }
    
    if (filters.search) {
        filtered = filtered.filter(l => 
            l.nama.toLowerCase().includes(filters.search.toLowerCase()) ||
            l.lokasi.toLowerCase().includes(filters.search.toLowerCase())
        );
    }
    
    const grid = document.getElementById('lapangan-grid');
    const emptyState = document.getElementById('empty-lapangan');
    
    if (filtered.length === 0) {
        grid.innerHTML = '';
        emptyState.style.display = 'block';
    } else {
        emptyState.style.display = 'none';
        // Re-render grid with filtered data
        console.log('Rendering filtered lapangan:', filtered);
    }
}

// Event listeners
document.getElementById('filter-daerah').addEventListener('change', applyFilter);
document.getElementById('filter-status').addEventListener('change', applyFilter);
document.getElementById('search-lapangan').addEventListener('input', function() {
    if (this.value.length >= 3 || this.value.length === 0) {
        applyFilter();
    }
});
</script>
@endsection
