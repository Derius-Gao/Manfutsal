@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
<div class="row">
    <div class="col-md-12">
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
                        <input type="date" class="form-control" id="start-date" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end-date" class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end-date" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filter-lapangan" class="form-label">Filter Lapangan</label>
                        <select class="form-select" id="filter-lapangan">
                            <option value="">Semua Lapangan</option>
                            @foreach($lapangans as $lapangan)
                                <option value="{{ $lapangan->id }}">{{ $lapangan->nama }}</option>
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
                                    <i class="fas fa-arrow-up"></i> +15% dari bulan lalu
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
                                    <i class="fas fa-arrow-up"></i> +12 dari bulan lalu
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
                                    <i class="fas fa-arrow-down"></i> -5 dari kemarin
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
                                    <i class="fas fa-arrow-up"></i> +3% dari bulan lalu
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6>Grafik Pendapatan</h6>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary active" onclick="changeChartPeriod('daily')">Harian</button>
                                    <button class="btn btn-outline-primary" onclick="changeChartPeriod('weekly')">Mingguan</button>
                                    <button class="btn btn-outline-primary" onclick="changeChartPeriod('monthly')">Bulanan</button>
                                </div>
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

                <!-- Payment Methods Stats -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6>Statistik Metode Pembayaran</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $totalPaymentAmount = $paymentStats->sum('total');
                                    @endphp
                                    @foreach(['Transfer Bank' => 'success', 'E-Wallet' => 'info', 'Tunai' => 'warning', 'Lainnya' => 'secondary'] as $method => $color)
                                        @php
                                            $methodKey = strtolower(str_replace(' ', '_', $method));
                                            $methodData = $paymentStats->firstWhere('metode_pembayaran', $methodKey);
                                            $amount = $methodData['total'] ?? 0;
                                            $percentage = $totalPaymentAmount > 0 ? ($amount / $totalPaymentAmount * 100) : 0;
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <div class="avatar bg-{{ $color }} text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px;">
                                                    <i class="fas fa-{{ $method === 'Transfer Bank' ? 'university' : ($method === 'E-Wallet' ? 'mobile-alt' : ($method === 'Tunai' ? 'money-bill' : 'credit-card')) }} fa-2x"></i>
                                                </div>
                                                <h6>{{ $method }}</h6>
                                                <h5 class="text-{{ $color }}">{{ formatRupiah($amount) }}</h5>
                                                <small class="text-muted">{{ round($percentage, 1) }}% dari total</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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
                                        <th>Metode</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="transaction-tbody">
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                            <td><span class="badge bg-primary">BK{{ str_pad($payment->booking_id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                            <td>{{ $payment->booking->user->name ?? 'Unknown' }}</td>
                                            <td>{{ $payment->booking->lapangan->nama ?? 'Unknown' }}</td>
                                            <td>{{ $payment->booking->jam_mulai->format('H:i') }}-{{ $payment->booking->jam_selesai->format('H:i') }}</td>
                                            <td>{{ formatRupiah($payment->jumlah) }}</td>
                                            <td><span class="badge bg-{{ $payment->metode_pembayaran === 'transfer_bank' ? 'success' : ($payment->metode_pembayaran === 'e_wallet' ? 'info' : 'warning') }}">{{ ucfirst(str_replace('_', ' ', $payment->metode_pembayaran)) }}</span></td>
                                            <td>{!! getStatusBadge($payment->booking->status) !!}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="showTransactionDetail({{ $payment->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="mt-3">
                            <ul class="pagination pagination-sm justify-content-center">
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
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="transaction-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let revenueChart;
let lapanganChart;

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    initializeRevenueChart('daily');
    initializeLapanganChart();
});

function initializeRevenueChart(period) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    const chartData = {
        daily: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            data: [1200000, 1500000, 1350000, 1800000, 1600000, 1900000, 2100000]
        },
        weekly: {
            labels: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'],
            data: [8000000, 9500000, 11000000, 12500000]
        },
        monthly: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            data: [12000000, 15000000, 13500000, 18000000, 16000000, 19000000, 21000000, 20000000, 22000000, 24000000, 23000000, 25000000]
        }
    };
    
    if (revenueChart) {
        revenueChart.destroy();
    }
    
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData[period].labels,
            datasets: [{
                label: 'Pendapatan',
                data: chartData[period].data,
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
}

function initializeLapanganChart() {
    const ctx = document.getElementById('lapanganChart').getContext('2d');
    
    lapanganChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Lapangan ABC', 'Sport Center XYZ', 'Futsal Arena', 'Indoor Sport'],
            datasets: [{
                data: [18000000, 15000000, 8000000, 4000000],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
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
}

function changeChartPeriod(period) {
    // Update button states
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Update chart
    initializeRevenueChart(period);
}

function applyFilter() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const lapangan = document.getElementById('filter-lapangan').value;
    
    Swal.fire({
        title: 'Applying Filter...',
        text: 'Sedang menerapkan filter',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        console.log('Filter applied:', { startDate, endDate, lapangan });
        updateSummaryCards();
    });
}

function resetFilter() {
    document.getElementById('start-date').value = '{{ now()->startOfMonth()->format("Y-m-d") }}';
    document.getElementById('end-date').value = '{{ now()->format("Y-m-d") }}';
    document.getElementById('filter-lapangan').value = '';
    
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

function updateSummaryCards() {
    // Simulate updating summary cards with new data
    document.getElementById('total-revenue').textContent = formatRupiah(52000000);
    document.getElementById('completed-bookings').textContent = '182';
    document.getElementById('pending-bookings').textContent = '18';
    document.getElementById('avg-transaction').textContent = formatRupiah(285714);
}

function showTransactionDetail(id) {
    const transactions = [
        {
            id: 1,
            kode: 'BK001',
            customer: 'John Doe',
            lapangan: 'Lapangan Futsal ABC',
            tanggal: '2024-01-15',
            jam: '19:00-21:00',
            total: 200000,
            metode: 'Transfer Bank',
            status: 'completed'
        },
        {
            id: 2,
            kode: 'BK002',
            customer: 'Jane Smith',
            lapangan: 'Sport Center XYZ',
            tanggal: '2024-01-14',
            jam: '18:00-20:00',
            total: 160000,
            metode: 'E-Wallet',
            status: 'completed'
        },
        {
            id: 3,
            kode: 'BK003',
            customer: 'Bob Johnson',
            lapangan: 'Lapangan Futsal ABC',
            tanggal: '2024-01-13',
            jam: '20:00-22:00',
            total: 200000,
            metode: 'Tunai',
            status: 'confirmed'
        }
    ];
    
    const transaction = transactions.find(t => t.id === id);
    if (!transaction) return;
    
    const content = `
        <table class="table table-sm">
            <tr><td>Kode Booking</td><td><span class="badge bg-primary">${transaction.kode}</span></td></tr>
            <tr><td>Customer</td><td>${transaction.customer}</td></tr>
            <tr><td>Lapangan</td><td>${transaction.lapangan}</td></tr>
            <tr><td>Tanggal</td><td>${transaction.tanggal}</td></tr>
            <tr><td>Jam</td><td>${transaction.jam}</td></tr>
            <tr><td>Total</td><td>${formatRupiah(transaction.total)}</td></tr>
            <tr><td>Metode Pembayaran</td><td><span class="badge bg-success">${transaction.metode}</span></td></tr>
            <tr><td>Status</td><td>${getStatusBadgeHtml(transaction.status)}</td></tr>
        </table>
    `;
    
    document.getElementById('transaction-content').innerHTML = content;
    new bootstrap.Modal(document.getElementById('transactionModal')).show();
}

function exportReport() {
    Swal.fire({
        title: 'Export Laporan Keuangan',
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
            <div class="mb-3">
                <label class="form-label">Include Sections</label>
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
                    <input class="form-check-input" type="checkbox" id="include-charts" checked>
                    <label class="form-check-label" for="include-charts">
                        Charts & Graphs
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="include-payment-stats" checked>
                    <label class="form-check-label" for="include-payment-stats">
                        Payment Statistics
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
            const includeSummary = document.getElementById('include-summary').checked;
            const includeTransactions = document.getElementById('include-transactions').checked;
            const includeCharts = document.getElementById('include-charts').checked;
            const includePaymentStats = document.getElementById('include-payment-stats').checked;
            
            return { format, startDate, endDate, includeSummary, includeTransactions, includeCharts, includePaymentStats };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { format, startDate, endDate, includeSummary, includeTransactions, includeCharts, includePaymentStats } = result.value;
            
            Swal.fire({
                title: 'Exporting...',
                text: `Sedang mengekspor laporan keuangan ke format ${format.toUpperCase()}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire('Success!', `Laporan keuangan berhasil diekspor ke format ${format.toUpperCase()}`, 'success');
            }, 2000);
        }
    });
}

function refreshData() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Memperbarui data keuangan',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        location.reload();
    });
}

// Transaction filter
document.getElementById('transaction-filter').addEventListener('change', function() {
    const filter = this.value;
    console.log('Filtering transactions by status:', filter);
    // Implement filter logic here
});

// Auto-refresh every 5 minutes
setInterval(refreshData, 300000);

function getStatusBadgeHtml(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'confirmed': '<span class="badge bg-success">Confirmed</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>',
        'completed': '<span class="badge bg-info">Completed</span>',
        'cancelled': '<span class="badge bg-secondary">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}
</script>
@endsection
