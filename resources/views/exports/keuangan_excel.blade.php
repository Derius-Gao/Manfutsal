<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kode Booking</th>
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
                <td>BK{{ str_pad($payment->booking_id, 3, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $payment->booking->user->name ?? 'Unknown' }}</td>
                <td>{{ $payment->booking->lapangan->nama ?? 'Unknown' }}</td>
                <td>{{ $payment->booking->jam_mulai->format('H:i') }}-{{ $payment->booking->jam_selesai->format('H:i') }}</td>
                <td>{{ formatRupiah($payment->jumlah) }}</td>
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
