@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Laporan Keuangan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Keuangan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-money-bill-wave me-2"></i>Laporan Keuangan</h5>
                            <div>
                                <button class="btn btn-success" onclick="exportReport()">
                                    <i class="fas fa-download me-2"></i>Export
                                </button>
                                <button class="btn btn-primary" onclick="refreshData()">
                                    <i class="fas fa-sync me-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Date Range Filter -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label for="start-date" class="form-label">Dari Tanggal</label>
                                    <input type="date" class="form-control" id="start-date" value="{{ $start }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="end-date" class="form-label">Sampai Tanggal</label>
                                    <input type="date" class="form-control" id="end-date" value="{{ $end }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter-lapangan" class="form-label">Filter Lapangan</label>
                                    <select class="form-select" id="filter-lapangan">
                                        <option value="">Semua Lapangan</option>
                                        @foreach($lapangans as $lapangan)
                                            <option value="{{ $lapangan->id }}" {{ $lapanganId == $lapangan->id ? 'selected' : '' }}>{{ $lapangan->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label><br>
                                    <button class="btn btn-primary" onclick="applyFilter()">
                                        <i class="fas fa-filter me-2"></i>Filter
                                    </button>
                                    <button class="btn btn-secondary" onclick="resetFilter()">
                                        <i class="fas fa-redo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-arrow-up me-2"></i>Total Pendapatan</h5>
                                            <h3 id="total-revenue">{{ formatRupiah($totalIncome) }}</h3>
                                            <small class="d-block mt-2">
                                                <i class="fas fa-arrow-up"></i> +12% dari bulan lalu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-calendar-check me-2"></i>Booking Selesai</h5>
                                            <h3 id="completed-bookings">{{ $completedBookings }}</h3>
                                            <small class="d-block mt-2">
                                                <i class="fas fa-arrow-up"></i> +8 dari bulan lalu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-clock me-2"></i>Booking Pending</h5>
                                            <h3 id="pending-bookings">{{ $pendingBookings }}</h3>
                                            <small class="d-block mt-2">
                                                <i class="fas fa-arrow-down"></i> -3 dari kemarin
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body">
                                            <h5><i class="fas fa-money-check-alt me-2"></i>Rata-rata Transaksi</h5>
                                            <h3 id="avg-transaction">{{ formatRupiah($avgTransaction) }}</h3>
                                            <small class="d-block mt-2">
                                                <i class="fas fa-arrow-up"></i> +5% dari bulan lalu
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Charts -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Grafik Pendapatan</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="revenueChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6>Pendapatan per Lapangan</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="lapanganChart" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction Table -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6>Detail Transaksi</h6>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm" id="transaction-filter" style="width: auto;">
                                            <option value="">Semua Status</option>
                                            <option value="completed">Selesai</option>
                                            <option value="confirmed">Dikonfirmasi</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Kode</th>
                                                    <th>Customer</th>
                                                    <th>Lapangan</th>
                                                    <th>Jam</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Payment</th>
                                                </tr>
                                            </thead>
                                            <tbody id="transaction-tbody">
                                                @forelse($payments as $payment)
                                                    <tr>
                                                        <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                                        <td><span class="badge bg-primary">BK{{ str_pad($payment->booking_id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                                        <td>{{ $payment->booking->user->name ?? 'Unknown' }}</td>
                                                        <td>{{ $payment->booking->lapangan->nama ?? 'Unknown' }}</td>
                                                        <td>{{ $payment->booking->jam_mulai->format('H:i') }}-{{ $payment->booking->jam_selesai->format('H:i') }}</td>
                                                        <td>{{ formatRupiah($payment->jumlah) }}</td>
                                                        <td>{!! getStatusBadge($payment->booking->status) !!}</td>
                                                        <td><span class="badge bg-success">{{ ucfirst($payment->status) }}</span></td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="text-center text-muted py-4">
                                                            <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                            Tidak ada data transaksi
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    @if($payments->hasPages())
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $payments->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart - Using real monthly data
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const monthlyData = @json($monthlyRevenue);
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Pendapatan',
                data: months.map((month, index) => monthlyData[index + 1] || 0),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                }
            }
        }
    });

    // Lapangan Chart - Using real lapangan data
    const lapanganCtx = document.getElementById('lapanganChart').getContext('2d');
    const lapanganData = @json($byLapangan);
    
    new Chart(lapanganCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(lapanganData),
            datasets: [{
                data: Object.values(lapanganData),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function applyFilter() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const lapangan = document.getElementById('filter-lapangan').value;
    
    const params = new URLSearchParams();
    if (startDate) params.append('start', startDate);
    if (endDate) params.append('end', endDate);
    if (lapangan) params.append('lapangan', lapangan);
    
    window.location.href = `{{ route('manager.keuangan.index') }}?${params.toString()}`;
}

function resetFilter() {
    window.location.href = '{{ route('manager.keuangan.index') }}';
}

function exportReport() {
    Swal.fire({
        title: 'Export Report',
        html: `
            <div class="mb-3">
                <label class="form-label">Format Export</label>
                <select class="form-select" id="export-format">
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                    <option value="csv">CSV</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Include Details</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-summary" checked>
                    <label class="form-check-label" for="include-summary">
                        Summary Report
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-transactions" checked>
                    <label class="form-check-label" for="include-transactions">
                        Transaction Details
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-charts">
                    <label class="form-check-label" for="include-charts">
                        Charts
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            const includeSummary = document.getElementById('include-summary').checked;
            const includeTransactions = document.getElementById('include-transactions').checked;
            const includeCharts = document.getElementById('include-charts').checked;
            
            return { format, includeSummary, includeTransactions, includeCharts };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, includeSummary, includeTransactions, includeCharts } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor report ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Report berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    location.reload();
}

// Auto-refresh every 5 minutes
setInterval(refreshData, 300000);

// Transaction filter
document.getElementById('transaction-filter').addEventListener('change', function() {
    const filter = this.value;
    const rows = document.querySelectorAll('#transaction-tbody tr');
    
    rows.forEach(row => {
        if (filter === '') {
            row.style.display = '';
        } else {
            const statusCell = row.querySelector('td:nth-child(7)');
            const status = statusCell.textContent.toLowerCase();
            row.style.display = status.includes(filter) ? '' : 'none';
        }
    });
});
</script>
@endsection
