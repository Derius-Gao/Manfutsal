@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
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
        <form method="GET" action="{{ route('keuangan.index') }}" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="start" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start" name="start" value="{{ $start }}">
                </div>
                <div class="col-md-3">
                    <label for="end" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end" name="end" value="{{ $end }}">
                </div>
                <div class="col-md-3">
                    <label for="lapangan" class="form-label">Filter Lapangan</label>
                    <select class="form-select" id="lapangan" name="lapangan">
                        <option value="">Semua Lapangan</option>
                        @foreach($lapangans as $lapangan)
                            <option value="{{ $lapangan->id }}" {{ $lapanganId == $lapangan->id ? 'selected' : '' }}>{{ $lapangan->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                    <a href="{{ route('keuangan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-arrow-up me-2"></i>Total Pendapatan</h5>
                        <h3>{{ formatRupiah($totalIncome) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-check me-2"></i>Booking Selesai</h5>
                        <h3>{{ $completedBookings }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-clock me-2"></i>Booking Pending</h5>
                        <h3>{{ $pendingBookings }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="fas fa-money-check-alt me-2"></i>Rata-rata Transaksi</h5>
                        <h3>{{ formatRupiah($avgTransaction) }}</h3>
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
            <div class="card-header">
                <h6>Detail Transaksi</h6>
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
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->created_at->format('Y-m-d') }}</td>
                                    <td><span class="badge bg-primary">BK{{ str_pad($payment->booking_id, 3, '0', STR_PAD_LEFT) }}</span></td>
                                    <td>{{ $payment->booking->user->name ?? 'Unknown' }}</td>
                                    <td>{{ $payment->booking->lapangan->nama ?? 'Unknown' }}</td>
                                    <td>
                                        @if($payment->booking->jam_mulai && $payment->booking->jam_selesai)
                                            {{ \Carbon\Carbon::parse($payment->booking->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($payment->booking->jam_selesai)->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
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

                @if($payments->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
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
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            }
        }
    });

    // Lapangan Chart
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

function refreshData() {
    location.reload();
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
        `,
        showCancelButton: true,
        confirmButtonText: 'Export',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const format = document.getElementById('export-format').value;
            return { format };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Success!', `Report berhasil diekspor ke format ${result.value.format.toUpperCase()}`, 'success');
        }
    });
}
</script>
@endsection

