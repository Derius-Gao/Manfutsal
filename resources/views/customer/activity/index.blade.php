@extends('layouts.app')

@section('title', 'Log Activity')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i>Log Activity</h5>
            </div>
            <div class="card-body">
                <!-- Filter -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="filter-action" class="form-label">Filter Action</label>
                        <select class="form-select" id="filter-action">
                            <option value="">Semua Activity</option>
                            <option value="login">Login</option>
                            <option value="booking_created">Booking Created</option>
                            <option value="payment_uploaded">Payment Uploaded</option>
                            <option value="booking_cancelled">Booking Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="filter-date" class="form-label">Filter Tanggal</label>
                        <input type="date" class="form-control" id="filter-date">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label><br>
                        <button class="btn btn-primary" onclick="filterActivities()">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilter()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="timeline" id="activity-timeline">
                    <!-- Sample activities -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Login</h6>
                                    <p class="text-muted mb-1">Anda berhasil login ke sistem</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>2 jam yang lalu
                                        <i class="fas fa-map-marker-alt ms-2 me-1"></i>192.168.1.1
                                    </small>
                                </div>
                                <span class="badge bg-success">Success</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Booking Created</h6>
                                    <p class="text-muted mb-1">Booking lapangan "Lapangan Futsal ABC" untuk tanggal 2024-01-15</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>5 jam yang lalu
                                        <i class="fas fa-map-marker-alt ms-2 me-1"></i>192.168.1.1
                                    </small>
                                </div>
                                <span class="badge bg-primary">Info</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Payment Uploaded</h6>
                                    <p class="text-muted mb-1">Upload bukti pembayaran untuk booking BK001</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>1 hari yang lalu
                                        <i class="fas fa-map-marker-alt ms-2 me-1"></i>192.168.1.1
                                    </small>
                                </div>
                                <span class="badge bg-warning">Pending</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-info">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Booking Confirmed</h6>
                                    <p class="text-muted mb-1">Booking BK001 telah dikonfirmasi oleh manager</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>2 hari yang lalu
                                        <i class="fas fa-map-marker-alt ms-2 me-1"></i>192.168.1.1
                                    </small>
                                </div>
                                <span class="badge bg-success">Success</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-activity" class="text-center py-5" style="display: none;">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada activity</h5>
                    <p class="text-muted">Belum ada aktivitas yang dilakukan</p>
                </div>

                <!-- Load More -->
                <div class="text-center mt-4">
                    <button class="btn btn-outline-primary" onclick="loadMore()">
                        <i class="fas fa-chevron-down me-2"></i>Muat Lebih Banyak
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}

.timeline-content {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
</style>

@section('scripts')
<script>
function filterActivities() {
    const action = document.getElementById('filter-action').value;
    const date = document.getElementById('filter-date').value;
    
    console.log('Filtering activities:', { action, date });
    
    // Implement filter logic here
    // This would typically make an AJAX call to the server
    Swal.fire({
        title: 'Filtering...',
        text: 'Sedang memfilter activity',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Update timeline with filtered results
        console.log('Filter applied');
    });
}

function resetFilter() {
    document.getElementById('filter-action').value = '';
    document.getElementById('filter-date').value = '';
    
    // Reset timeline to show all activities
    console.log('Filter reset');
}

function loadMore() {
    Swal.fire({
        title: 'Loading...',
        text: 'Memuat activity lebih banyak',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        // Add more activities to timeline
        console.log('More activities loaded');
    });
}

// Auto-refresh activity every 30 seconds
setInterval(() => {
    console.log('Refreshing activities...');
    // Implement auto-refresh logic here
}, 30000);
</script>
@endsection
