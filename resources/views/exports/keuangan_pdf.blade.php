<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary-box {
            display: inline-block;
            width: 23%;
            margin-right: 1%;
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .summary-box h3 {
            margin: 5px 0;
            color: #333;
        }
        .summary-box p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <p>Periode: {{ $startDate ?? 'Semua' }} - {{ $endDate ?? 'Semua' }}</p>
        <p>Dibuat: {{ date('d-m-Y H:i:s') }}</p>
    </div>

    @if($includeSummary)
    <div class="summary">
        <div class="summary-box">
            <h3>{{ function_exists('formatRupiah') ? formatRupiah($totalIncome) : 'Rp ' . number_format($totalIncome, 0, ',', '.') }}</h3>
            <p>Total Pendapatan</p>
        </div>
        <div class="summary-box">
            <h3>{{ $completedBookings }}</h3>
            <p>Booking Selesai</p>
        </div>
        <div class="summary-box">
            <h3>{{ $payments->count() }}</h3>
            <p>Total Transaksi</p>
        </div>
        <div class="summary-box">
            <h3>{{ function_exists('formatRupiah') ? formatRupiah($avgTransaction) : 'Rp ' . number_format($avgTransaction, 0, ',', '.') }}</h3>
            <p>Rata-rata Transaksi</p>
        </div>
    </div>
    @endif

    @if($includeTransactions)
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Booking</th>
                <th>Customer</th>
                <th>Lapangan</th>
                <th>Jam</th>
                <th class="text-right">Total</th>
                <th>Status</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->created_at->format('d-m-Y') }}</td>
                    <td>BK{{ str_pad($payment->booking_id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $payment->booking->user->name ?? 'Unknown' }}</td>
                    <td>{{ $payment->booking->lapangan->nama ?? 'Unknown' }}</td>
                    <td>{{ $payment->booking->jam_mulai->format('H:i') }}-{{ $payment->booking->jam_selesai->format('H:i') }}</td>
                    <td class="text-right">{{ function_exists('formatRupiah') ? formatRupiah($payment->jumlah) : 'Rp ' . number_format($payment->jumlah, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($payment->booking->status) }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data transaksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($includeSummary && $byLapangan->count() > 0)
    <h3>Pendapatan per Lapangan</h3>
    <table>
        <thead>
            <tr>
                <th>Lapangan</th>
                <th class="text-right">Total Pendapatan</th>
                <th class="text-right">Jumlah Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byLapangan as $lapanganName => $total)
                @php
                    $lapanganPayments = $payments->filter(function($p) use ($lapanganName) {
                        return ($p->booking->lapangan->nama ?? 'Unknown') === $lapanganName;
                    });
                @endphp
                <tr>
                    <td>{{ $lapanganName }}</td>
                    <td class="text-right">{{ function_exists('formatRupiah') ? formatRupiah($total) : 'Rp ' . number_format($total, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $lapanganPayments->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Manfutsal</p>
    </div>
</body>
</html>