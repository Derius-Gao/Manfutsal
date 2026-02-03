<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Payment;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function dashboard()
    {
        return view('customer.dashboard');
    }
    
    public function lapanganIndex()
    {
        $lapangans = Lapangan::active()->get();
        return view('customer.lapangan.index', compact('lapangans'));
    }
    
    public function bookingIndex()
    {
        $bookings = Booking::with(['lapangan', 'payment'])->where('user_id', auth()->id())->orderBy('tanggal', 'desc')->paginate(10);
        return view('customer.booking.index', compact('bookings'));
    }

    public function createBooking()
    {
        $lapangans = Lapangan::active()->get();
        return view('customer.booking.create', compact('lapangans'));
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'metode_pembayaran' => 'required|string',
        ]);

        $lapangan = Lapangan::findOrFail($data['lapangan_id']);
        // Calculate price (simple: per hour * duration)
        $start = \Carbon\Carbon::parse($data['jam_mulai']);
        $end = \Carbon\Carbon::parse($data['jam_selesai']);
        $hours = max(1, $start->diffInHours($end));
        $total = $lapangan->harga_per_jam * $hours;

        $booking = Booking::create([
            'user_id' => auth()->id(),
            'lapangan_id' => $lapangan->id,
            'tanggal' => $data['tanggal'],
            'jam_mulai' => $data['jam_mulai'],
            'jam_selesai' => $data['jam_selesai'],
            'total_harga' => $total,
            'status' => ($data['metode_pembayaran'] === 'cash') ? 'confirmed' : 'pending',
            'catatan' => $request->input('catatan'),
        ]);

        // Create payment record depending on method
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'jumlah' => $total,
            'metode_pembayaran' => $data['metode_pembayaran'],
            'status' => ($data['metode_pembayaran'] === 'cash') ? 'verified' : 'pending',
        ]);

        return redirect()->route('customer.booking.index')->with('success', 'Booking berhasil dibuat');
    }

    public function cancelBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            return back()->with('error', 'Akses ditolak');
        }
        $booking->status = 'cancelled';
        $booking->save();
        return back()->with('success', 'Booking dibatalkan');
    }
    
    public function activityIndex()
    {
        return view('customer.activity.index');
    }
}
