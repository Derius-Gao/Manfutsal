<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\User;
use App\Models\Lapangan;
use App\Models\Payment;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isCustomer()) {
            $bookings = Booking::where('user_id', $user->id)->orderBy('tanggal', 'desc')->limit(5)->get();
            $totalBookings = Booking::where('user_id', $user->id)->count();
            $pendingBookings = Booking::where('user_id', $user->id)->where('status', 'pending')->count();
            
            return view('dashboard', compact('bookings', 'totalBookings', 'pendingBookings'));
        }
        
        if ($user->isManager()) {
            $bookings = Booking::with(['user', 'lapangan'])->orderBy('tanggal', 'desc')->limit(5)->get();
            $pendingCount = Booking::where('status', 'pending')->count();
            $todayCount = Booking::whereDate('tanggal', now()->toDateString())->count();
            
            return view('dashboard', compact('bookings', 'pendingCount', 'todayCount'));
        }
        
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $totalUsers = User::count();
            $totalBookings = Booking::count();
            $totalLapangans = Lapangan::count();
            $totalIncome = Payment::where('status', 'verified')->sum('jumlah');
            $pendingBookings = Booking::where('status', 'pending')->count();
            $recentBookings = Booking::with(['user', 'lapangan'])->orderBy('created_at', 'desc')->limit(5)->get();
            
            return view('dashboard', compact('totalUsers', 'totalBookings', 'totalLapangans', 'totalIncome', 'pendingBookings', 'recentBookings'));
        }
        
        return view('dashboard');
    }
}
