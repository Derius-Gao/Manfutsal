<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Activity;
use App\Models\Lapangan;

class ManagerController extends Controller
{
    public function dashboard()
    {
        return view('manager.dashboard');
    }
    
    public function bookingIndex()
    {
        $query = Booking::with(['user', 'lapangan', 'payment'])->orderBy('tanggal', 'desc');

        $bookings = $query->paginate(20);

        // Basic stats
        $pendingCount = Booking::where('status', 'pending')->count();
        $confirmedCount = Booking::where('status', 'confirmed')->count();
        $rejectedCount = Booking::where('status', 'rejected')->count();
        $todayCount = Booking::whereDate('tanggal', now()->toDateString())->count();

        return view('manager.booking.index', compact('bookings', 'pendingCount', 'confirmedCount', 'rejectedCount', 'todayCount'));
    }
    
    public function keuanganIndex()
    {
        $start = request()->input('start', now()->startOfMonth()->format('Y-m-d'));
        $end = request()->input('end', now()->format('Y-m-d'));
        $lapanganId = request()->input('lapangan');
        
        $query = Payment::with(['booking.user', 'booking.lapangan'])
            ->where('status', 'verified');
            
        if ($start && $end) {
            $query->whereHas('booking', function ($qb) use ($start, $end) {
                $qb->whereBetween('tanggal', [$start, $end]);
            });
        }
        
        if ($lapanganId) {
            $query->whereHas('booking', function ($qb) use ($lapanganId) {
                $qb->where('lapangan_id', $lapanganId);
            });
        }
        
        $payments = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $totalIncome = $payments->sum('jumlah');
        $completedBookings = Booking::where('status', 'completed')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end]);
            })->count();
        $pendingBookings = Booking::where('status', 'pending')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereBetween('tanggal', [$start, $end]);
            })->count();
        $avgTransaction = $payments->count() > 0 ? $totalIncome / $payments->count() : 0;
        
        // Revenue by lapangan
        $byLapangan = $payments->groupBy(function ($p) {
            return $p->booking->lapangan->nama ?? 'Unknown';
        })->map(function ($group) {
            return $group->sum('jumlah');
        });
        
        // Get all lapangan for filter
        $lapangans = Lapangan::all();
        
        // Monthly revenue data for chart
        $monthlyRevenue = Payment::where('status', 'verified')
            ->selectRaw('MONTH(created_at) as month, SUM(jumlah) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
        
        return view('manager.keuangan.index', compact(
            'payments',
            'totalIncome',
            'completedBookings', 
            'pendingBookings',
            'avgTransaction',
            'byLapangan',
            'lapangans',
            'monthlyRevenue',
            'start',
            'end',
            'lapanganId'
        ));
    }
    
    public function activityIndex()
    {
        $action = request()->input('action');
        $userId = request()->input('user');
        $date = request()->input('date');
        
        $query = Activity::with('user');
        
        if ($action) {
            $query->where('action', $action);
        }
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        $activities = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get unique actions for filter
        $actionList = Activity::distinct('action')->pluck('action')->filter();
        
        // Get users for filter
        $users = User::select('id', 'name', 'role')->orderBy('name')->get();
        
        // Calculate statistics
        $totalActivities = Activity::count();
        $loginActivities = Activity::where('action', 'login')->count();
        $bookingActivities = Activity::whereIn('action', ['booking_created', 'booking_confirmed', 'booking_rejected', 'booking_completed'])->count();
        $paymentActivities = Activity::whereIn('action', ['payment_uploaded', 'payment_verified'])->count();
        
        return view('manager.activity.index', compact(
            'activities',
            'actionList',
            'users',
            'totalActivities',
            'loginActivities',
            'bookingActivities',
            'paymentActivities',
            'action',
            'userId',
            'date'
        ));
    }

    public function confirm(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'confirmed';
        $booking->save();
        return back()->with('success', 'Booking berhasil dikonfirmasi');
    }

    public function reject(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $reason = $request->input('reason');
        $booking->status = 'rejected';
        $booking->catatan = $reason ?? $booking->catatan;
        $booking->save();
        return back()->with('success', 'Booking berhasil ditolak');
    }

    public function complete(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'completed';
        $booking->save();
        return back()->with('success', 'Booking ditandai selesai');
    }
    public function detail($id)
{
    $booking = Booking::with(['user', 'lapangan', 'payment'])->findOrFail($id);
    return response()->json(['booking' => $booking]);
}
}
