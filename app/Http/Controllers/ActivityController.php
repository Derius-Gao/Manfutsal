<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $query = Activity::with('user')->orderBy('created_at', 'desc');
        
        // Filter berdasarkan role
        if (Auth::user()->isCustomer()) {
            $query->where('user_id', Auth::id());
        }
        
        // Filter berdasarkan action
        if (request()->has('action') && request()->action) {
            $query->where('action', request()->action);
        }
        
        // Filter berdasarkan user
        if (request()->has('user') && request()->user) {
            $query->where('user_id', request()->user);
        }
        
        // Filter berdasarkan tanggal
        if (request()->has('date') && request()->date) {
            $query->whereDate('created_at', request()->date);
        }
        
        $activities = $query->paginate(20);
        
        // Stats
        $statsQuery = Activity::query();
        if (Auth::user()->isCustomer()) {
            $statsQuery->where('user_id', Auth::id());
        }
        
        $totalActivities = (clone $statsQuery)->count();
        $loginActivities = (clone $statsQuery)->where('action', 'login')->count();
        $bookingActivities = (clone $statsQuery)->whereIn('action', ['booking_created', 'booking_confirmed', 'booking_rejected', 'booking_completed', 'booking_cancelled'])->count();
        $paymentActivities = (clone $statsQuery)->whereIn('action', ['payment_uploaded', 'payment_verified'])->count();
        
        // Get unique actions for filter
        $actionList = Activity::distinct('action')->pluck('action')->filter()->toArray();
        
        // Get users for filter (hanya untuk admin)
        $users = [];
        if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin() || Auth::user()->isManager()) {
            $users = User::select('id', 'name', 'role')->orderBy('name')->get();
        }
        
        return view('activities.index', compact('activities', 'totalActivities', 'loginActivities', 'bookingActivities', 'paymentActivities', 'actionList', 'users'));
    }

    public function show($id)
    {
        $activity = Activity::with('user')->findOrFail($id);
        
        // Check authorization
        if (Auth::user()->isCustomer() && $activity->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('activities.show', compact('activity'));
    }
}
