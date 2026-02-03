<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Lapangan;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $payments = Payment::where('status', 'verified')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereHas('booking', function ($qb) use ($start, $end) {
                    $qb->whereBetween('tanggal', [$start, $end]);
                });
            })
            ->with(['booking.lapangan'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Basic totals
        $totalIncome = $payments->sum('jumlah');

        $byLapangan = $payments->groupBy(function ($p) {
            return $p->booking->lapangan->nama ?? 'Unknown';
        })->map(function ($group) {
            return $group->sum('jumlah');
        });

        return view('manager.keuangan.index', compact('payments', 'totalIncome', 'byLapangan'));
    }
}
