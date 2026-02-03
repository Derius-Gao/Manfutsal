@extends('layouts.app')

@section('title', 'Log Activity')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-history me-2"></i>Log Activity</h5>
                <div>
                    <button class="btn btn-success" onclick="exportLog()">
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
                    <div class="col-md-3">
                        <label for="filter-action" class="form-label">Filter Action</label>
                        <select class="form-select" id="filter-action">
                            <option value="">Semua Activity</option>
                            <option value="login">Login</option>
                            <option value="booking_created">Booking Created</option>
                            <option value="booking_confirmed">Booking Confirmed</option>
                            <option value="booking_rejected">Booking Rejected</option>
                            <option value="booking_completed">Booking Completed</option>
                            <option value="payment_uploaded">Payment Uploaded</option>
                            <option value="payment_verified">Payment Verified</option>
                            <option value="user_registered">User Registered</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-user" class="form-label">Filter User</label>
                        <select class="form-select" id="filter-user">
                            <option value="">Semua User</option>
                            <option value="1">John Doe (Customer)</option>
                            <option value="2">Jane Smith (Customer)</option>
                            <option value="3">Admin User (Admin)</option>
                            <option value="4">Manager User (Manager)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter-date" class="form-label">Filter Tanggal</label>
                        <input type="date" class="form-control" id="filter-date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label><br>
                        <button class="btn btn-primary" onclick="filterActivities()">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilter()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-list me-2"></i>Total Activity</h5>
                                <h3 id="total-activity">156</h3>
                                <small>Hari ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-sign-in-alt me-2"></i>Login Activity</h5>
                                <h3 id="login-activity">42</h3>
                                <small>Total login</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-calendar me-2"></i>Booking Activity</h5>
                                <h3 id="booking-activity">68</h3>
                                <small>Total booking</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5><i class="fas fa-money-bill me-2"></i>Payment Activity</h5>
                                <h3 id="payment-activity">46</h3>
                                <small>Total payment</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Timeline -->
                <div class="timeline" id="activity-timeline">
                    <!-- Sample activities -->
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Booking Confirmed</h6>
                                    <p class="text-muted mb-1">
                                        Manager mengkonfirmasi booking <strong>BK001</strong> untuk customer <strong>John Doe</strong>
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>2 jam yang lalu
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Manager User
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>192.168.1.10
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-success">Success</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Payment Uploaded</h6>
                                    <p class="text-muted mb-1">
                                        Customer <strong>Jane Smith</strong> upload bukti pembayaran untuk booking <strong>BK002</strong>
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>3 jam yang lalu
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Jane Smith
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>192.168.1.20
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-primary">Info</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-warning">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Booking Created</h6>
                                    <p class="text-muted mb-1">
                                        Customer <strong>Bob Johnson</strong> membuat booking untuk <strong>Lapangan Futsal ABC</strong> pada tanggal 2024-01-20
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>5 jam yang lalu
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Bob Johnson
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>192.168.1.30
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-warning">Warning</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-info">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Login</h6>
                                    <p class="text-muted mb-1">
                                        User <strong>Alice Brown</strong> berhasil login ke sistem
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>6 jam yang lalu
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Alice Brown
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>192.168.1.40
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-info">Info</span>
                            </div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Booking Rejected</h6>
                                    <p class="text-muted mb-1">
                                        Manager menolak booking <strong>BK003</strong> untuk customer <strong>Charlie Wilson</strong> - Alasan: Jadwal bentrok
                                    </p>
                                    <div class="d-flex align-items-center gap-3">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>8 jam yang lalu
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Manager User
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>192.168.1.10
                                        </small>
                                    </div>
                                </div>
                                <span class="badge bg-danger">Error</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-activity" class="text-center py-5" style="display: none;">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada activity</h5>
                    <p class="text-muted">Tidak ada aktivitas yang cocok dengan filter</p>
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
    const user = document.getElementById('filter-user').value;
    const date = document.getElementById('filter-date').value;
    
    Swal.fire({
        title: 'Filtering...',
        text: 'Sedang memfilter activity',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        console.log('Filter applied:', { action, user, date });
        updateActivityStats();
    });
}

function resetFilter() {
    document.getElementById('filter-action').value = '';
    document.getElementById('filter-user').value = '';
    document.getElementById('filter-date').value = '{{ now()->format("Y-m-d") }}';
    
    Swal.fire({
        title: 'Reset Filter...',
        text: 'Mengatur ulang filter',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        location.reload();
    });
}

function updateActivityStats() {
    // Simulate updating stats based on filter
    document.getElementById('total-activity').textContent = '124';
    document.getElementById('login-activity').textContent = '35';
    document.getElementById('booking-activity').textContent = '52';
    document.getElementById('payment-activity').textContent = '37';
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
        console.log('More activities loaded');
    });
}

function exportLog() {
    Swal.fire({
        title: 'Export Activity Log',
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
                        <input type="date" class="form-control" id="export-start" value="{{ now()->subDays(7)->format('Y-m-d') }}">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" id="export-end" value="{{ now()->format('Y-m-d') }}">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Include Fields</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-user-info" checked>
                    <label class="form-check-label" for="include-user-info">
                        User Information
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-action" checked>
                    <label class="form-check-label" for="include-action">
                        Action Details
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-ip" checked>
                    <label class="form-check-label" for="include-ip">
                        IP Address
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
            const includeUser = document.getElementById('include-user-info').checked;
            const includeAction = document.getElementById('include-action').checked;
            const includeIp = document.getElementById('include-ip').checked;
            
            return { format, startDate, endDate, includeUser, includeAction, includeIp };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, startDate, endDate, includeUser, includeAction, includeIp } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor activity log ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Activity log berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Memperbarui data activity',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        location.reload();
    });
}

// Auto-refresh every 2 minutes
setInterval(refreshData, 120000);

// Real-time updates simulation
function addNewActivity() {
    const timeline = document.getElementById('activity-timeline');
    const newActivity = document.createElement('div');
    newActivity.className = 'timeline-item';
    newActivity.innerHTML = `
        <div class="timeline-marker bg-success">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="timeline-content">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">New Activity</h6>
                    <p class="text-muted mb-1">This is a new activity added in real-time</p>
                    <div class="d-flex align-items-center gap-3">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>Baru saja
                        </small>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>System
                        </small>
                    </div>
                </div>
                <span class="badge bg-success">New</span>
            </div>
        </div>
    `;
    
    timeline.insertBefore(newActivity, timeline.firstChild);
    
    // Remove last item if too many
    const items = timeline.querySelectorAll('.timeline-item');
    if (items.length > 10) {
        timeline.removeChild(items[items.length - 1]);
    }
}

// Simulate real-time activity every 30 seconds
setInterval(() => {
    if (Math.random() > 0.7) { // 30% chance
        addNewActivity();
    }
}, 30000);
</script>
@endsection
