<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Payment;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Booking::with(['user', 'lapangan', 'payment'])->orderBy('tanggal', 'desc');
        
        // Filter berdasarkan role
        if ($user->isCustomer()) {
            $query->where('user_id', $user->id);
        }
        
        // Filter berdasarkan status
        if (request()->has('status') && request()->status) {
            $query->where('status', request()->status);
        }
        
        // Filter berdasarkan tanggal
        if (request()->has('tanggal') && request()->tanggal) {
            $query->whereDate('tanggal', request()->tanggal);
        }
        
        // Filter berdasarkan lapangan
        if (request()->has('lapangan_id') && request()->lapangan_id) {
            $query->where('lapangan_id', request()->lapangan_id);
        }
        
        // Search
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhereHas('lapangan', function($subQ) use ($search) {
                    $subQ->where('nama', 'like', '%' . $search . '%');
                });
            });
        }
        
        $bookings = $query->paginate(20);
        
        // Stats
        $statsQuery = Booking::query();
        if ($user->isCustomer()) {
            $statsQuery->where('user_id', $user->id);
        }
        
        $pendingCount = (clone $statsQuery)->where('status', 'pending')->count();
        $confirmedCount = (clone $statsQuery)->where('status', 'confirmed')->count();
        $rejectedCount = (clone $statsQuery)->where('status', 'rejected')->count();
        $todayCount = (clone $statsQuery)->whereDate('tanggal', now()->toDateString())->count();
        $cancelledCount = (clone $statsQuery)->where('status', 'cancelled')->count();
        
        $lapangans = Lapangan::all();
        
        return view('bookings.index', compact('bookings', 'pendingCount', 'confirmedCount', 'rejectedCount', 'todayCount', 'cancelledCount', 'lapangans'));
    }

    public function create()
    {
        $lapangans = Lapangan::where('status', 'aktif')->get();
        return view('bookings.create', compact('lapangans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'metode_pembayaran' => 'required|in:cash,transfer_bank,ewallet',
        ]);
        
        $lapangan = Lapangan::findOrFail($request->lapangan_id);
        
        // Check availability
        $isAvailable = !Booking::where('lapangan_id', $lapangan->id)
            ->where('tanggal', $request->tanggal)
            ->where('status', '!=', 'cancelled')
            ->where(function($q) use ($request) {
                $q->where(function($subQ) use ($request) {
                    $subQ->where('jam_mulai', '>=', $request->jam_mulai)
                          ->where('jam_mulai', '<', $request->jam_selesai);
                })->orWhere(function($subQ) use ($request) {
                    $subQ->where('jam_selesai', '>', $request->jam_mulai)
                          ->where('jam_selesai', '<=', $request->jam_selesai);
                })->orWhere(function($subQ) use ($request) {
                    $subQ->where('jam_mulai', '<=', $request->jam_mulai)
                          ->where('jam_selesai', '>=', $request->jam_selesai);
                });
            })
            ->exists();
        
        if (!$isAvailable) {
            return back()->withErrors(['jam_mulai' => 'Lapangan tidak tersedia pada waktu tersebut'])->withInput();
        }
        
        // Calculate price
        $start = \Carbon\Carbon::parse($request->jam_mulai);
        $end = \Carbon\Carbon::parse($request->jam_selesai);
        $hours = max(1, $start->diffInHours($end));
        $total = $lapangan->harga_per_jam * $hours;
        
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'lapangan_id' => $lapangan->id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'total_harga' => $total,
            'status' => 'pending', // Semua booking mulai dari pending, akan dikonfirmasi manager setelah payment verified
            'catatan' => $request->catatan,
        ]);
        
        // Create payment - jika cash langsung verified, jika transfer/ewallet pending
        Payment::create([
            'booking_id' => $booking->id,
            'jumlah' => $total,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => ($request->metode_pembayaran === 'cash') ? 'verified' : 'pending',
        ]);
        
        // Jika cash, langsung confirmed
        if ($request->metode_pembayaran === 'cash') {
            $booking->update(['status' => 'confirmed']);
        }
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_created',
            'description' => 'Membuat booking baru untuk ' . $lapangan->nama,
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dibuat');
    }

    public function show($id)
    {
        $booking = Booking::with(['user', 'lapangan', 'payment'])->findOrFail($id);
        
        // Check authorization
        if (Auth::user()->isCustomer() && $booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (request()->wantsJson()) {
            return response()->json(['booking' => $booking]);
        }
        
        return view('bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check authorization
        if (Auth::user()->isCustomer() && $booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return redirect()->route('bookings.index')->with('error', 'Booking tidak dapat diedit');
        }
        
        $lapangans = Lapangan::where('status', 'aktif')->get();
        return view('bookings.edit', compact('booking', 'lapangans'));
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check authorization
        if (Auth::user()->isCustomer() && $booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'status' => 'required|in:pending,confirmed,rejected,completed,cancelled',
            'catatan' => 'nullable|string',
        ]);
        
        $booking->update([
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_updated',
            'description' => 'Mengupdate booking ' . $booking->id,
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Booking berhasil diperbarui');
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check authorization
        if (Auth::user()->isCustomer() && $booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->delete();
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_deleted',
            'description' => 'Menghapus booking ' . $id,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dihapus');
    }

    public function confirm($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Only manager/admin can confirm
        if (!Auth::user()->isManager() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $booking->update(['status' => 'confirmed']);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_confirmed',
            'description' => 'Mengkonfirmasi booking ' . $booking->id . ' untuk customer ' . $booking->user->name,
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', 'Booking berhasil dikonfirmasi');
    }

    public function reject(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        // Only manager/admin can reject
        if (!Auth::user()->isManager() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $booking->update([
            'status' => 'rejected',
            'catatan' => $request->input('reason', $booking->catatan),
        ]);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_rejected',
            'description' => 'Menolak booking ' . $booking->id . ' untuk customer ' . $booking->user->name,
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', 'Booking berhasil ditolak');
    }

    public function complete($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Only manager/admin can complete
        if (!Auth::user()->isManager() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $booking->update(['status' => 'completed']);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_completed',
            'description' => 'Menyelesaikan booking ' . $booking->id,
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', 'Booking ditandai selesai');
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Check authorization
        if (Auth::user()->isCustomer() && $booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Booking tidak dapat dibatalkan');
        }
        
        $booking->update(['status' => 'cancelled']);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'booking_cancelled',
            'description' => 'Membatalkan booking ' . $booking->id,
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', 'Booking berhasil dibatalkan');
    }
}
