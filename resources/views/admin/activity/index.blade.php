@extends('layouts.app')

@section('title', 'Log Activity')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-history me-2"></i>Log Activity</h5>
                <div>
                    <button class="btn btn-success" onclick="exportActivities()">
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
                        <select class="form-select" id="filter-action">
                            <option value="">Semua Aksi</option>
                            @foreach($actionList as $act)
                                <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ ucfirst($act) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="filter-start" placeholder="Tanggal Mulai" value="{{ $start ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="filter-end" placeholder="Tanggal Selesai" value="{{ $end ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-activity" placeholder="Cari activity..." value="{{ $search ?? '' }}">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-sm" onclick="applyFilter()">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="resetFilter()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Total Activity</h5>
                                <h3 id="total-activities">{{ $totalActivities }}</h3>
                                <small>Semua aktivitas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Hari Ini</h5>
                                <h3 id="today-activities">{{ $todayActivities }}</h3>
                                <small>Aktivitas hari ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="mb-1">Rata-rata/Hari</h5>
                                <h3 id="avg-activities">{{ $totalActivities > 0 ? round($totalActivities / max(1, Activity::count() > 0 ? Activity::min('created_at')->diffInDays(now()) : 1), 1) : 0 }}</h3>
                                <small>Aktivitas per hari</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-sm" id="activity-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Deskripsi</th>
                                <th>IP Address</th>
                                <th>Browser</th>
                            </tr>
                        </thead>
                        <tbody id="activity-tbody">
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <small>{{ $activity->created_at->format('Y-m-d H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($activity->user)
                                            <div class="d-flex align-items-center">
                                                @if($activity->user->profile_photo)
                                                    <img src="{{ asset('storage/' . $activity->user->profile_photo) }}" class="rounded-circle me-2" style="width: 24px; height: 24px; object-fit: cover;" alt="{{ $activity->user->name }}">
                                                @else
                                                    <div class="avatar-xs bg-{{ $activity->user->role === 'customer' ? 'primary' : 'success' }} text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                        {{ substr($activity->user->name, 0, 2) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div>{{ $activity->user->name }}</div>
                                                    <small class="text-muted">{{ $activity->user->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->action === 'create' ? 'success' : ($activity->action === 'update' ? 'warning' : ($activity->action === 'delete' ? 'danger' : 'info')) }}">
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td><small>{{ $activity->ip_address ?? '-' }}</small></td>
                                    <td><small>{{ $activity->user_agent ? substr($activity->user_agent, 0, 30) . '...' : '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination pagination-sm justify-content-center">
                        @if($activities->hasPages())
                            {{ $activities->links() }}
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-xs {
    width: 24px;
    height: 24px;
    font-size: 10px;
}
</style>

@section('scripts')
<script>
function applyFilter() {
    const action = document.getElementById('filter-action').value;
    const start = document.getElementById('filter-start').value;
    const end = document.getElementById('filter-end').value;
    const search = document.getElementById('search-activity').value;
    
    const params = new URLSearchParams();
    if (action) params.append('action', action);
    if (start) params.append('start', start);
    if (end) params.append('end', end);
    if (search) params.append('search', search);
    
    window.location.href = `{{ route('admin.activity.index') }}?${params.toString()}`;
}

function resetFilter() {
    window.location.href = '{{ route('admin.activity.index') }}';
}

function refreshData() {
    location.reload();
}

function exportActivities() {
    Swal.fire({
        title: 'Export Activities',
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
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            const startDate = document.getElementById('export-start').value;
            const endDate = document.getElementById('export-end').value;
            
            return { format, startDate, endDate };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, startDate, endDate } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor activity ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Activity berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}
</script>
@endsection
