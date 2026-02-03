<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\User;
use App\Models\Activity;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }
    
    public function userIndex()
    {
        $role = request()->input('role');
        $status = request()->input('status');
        $search = request()->input('search');
        
        $query = User::query();
        
        if ($role) {
            $query->where('role', $role);
        }
        
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $totalUsers = User::count();
        $customerCount = User::where('role', 'customer')->count();
        $staffCount = User::whereIn('role', ['manager', 'admin', 'superadmin'])->count();
        $activeCount = User::where('is_active', true)->count();
        
        return view('admin.user.index', compact(
            'users',
            'totalUsers',
            'customerCount', 
            'staffCount',
            'activeCount',
            'role',
            'status',
            'search'
        ));
    }
    
    public function userCreate()
    {
        return view('admin.user.create');
    }
    
    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,manager,admin,superadmin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->is_active ?? true
        ]);
        
        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan');
    }
    
    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }
    
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:customer,manager,admin,superadmin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->is_active ?? true
        ]);
        
        if ($request->password) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $user->update(['password' => bcrypt($request->password)]);
        }
        
        return redirect()->route('admin.user.index')->with('success', 'User berhasil diperbarui');
    }
    
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus');
    }
    
    public function userToggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.user.index')->with('success', "User berhasil {$status}");
    }
    
    public function lapanganIndex()
    {
        $daerah = request()->input('daerah');
        $status = request()->input('status');
        $search = request()->input('search');
        
        $query = Lapangan::query();
        
        if ($daerah) {
            $query->where('daerah', $daerah);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        }
        
        $lapangans = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $totalLapangan = Lapangan::count();
        $activeLapangan = Lapangan::where('status', 'aktif')->count();
        $inactiveLapangan = Lapangan::where('status', 'tidak_aktif')->count();
        $avgPrice = Lapangan::avg('harga_per_jam');
        
        // Get unique daerah for filter
        $daerahList = Lapangan::distinct('daerah')->pluck('daerah')->filter();
        
        return view('admin.lapangan.index', compact(
            'lapangans',
            'totalLapangan',
            'activeLapangan', 
            'inactiveLapangan',
            'avgPrice',
            'daerahList',
            'daerah',
            'status',
            'search'
        ));
    }
    
    public function lapanganCreate()
    {
        return view('admin.lapangan.create');
    }
    
    public function lapanganStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'harga_per_jam' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);
        
        $lapangan = Lapangan::create([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'daerah' => $request->daerah,
            'kapasitas' => $request->kapasitas,
            'harga_per_jam' => $request->harga_per_jam,
            'fasilitas' => $request->fasilitas,
            'status' => $request->status
        ]);
        
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('lapangan', 'public');
            $lapangan->update(['foto' => $path]);
        }
        
        return redirect()->route('admin.lapangan.index')->with('success', 'Lapangan berhasil ditambahkan');
    }
    
    public function lapanganShow($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        return response()->json(['lapangan' => $lapangan]);
    }
    
    public function lapanganEdit($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        return view('admin.lapangan.edit', compact('lapangan'));
    }
    
    public function lapanganUpdate(Request $request, $id)
    {
        $lapangan = Lapangan::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'harga_per_jam' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif'
        ]);
        
        $lapangan->update([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'daerah' => $request->daerah,
            'kapasitas' => $request->kapasitas,
            'harga_per_jam' => $request->harga_per_jam,
            'fasilitas' => $request->fasilitas,
            'status' => $request->status
        ]);
        
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('lapangan', 'public');
            $lapangan->update(['foto' => $path]);
        }
        
        return redirect()->route('admin.lapangan.index')->with('success', 'Lapangan berhasil diperbarui');
    }
    
    public function lapanganDestroy($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        $lapangan->delete();
        
        return redirect()->route('admin.lapangan.index')->with('success', 'Lapangan berhasil dihapus');
    }
    
    public function lapanganToggleStatus($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        $newStatus = $lapangan->status === 'aktif' ? 'tidak_aktif' : 'aktif';
        $lapangan->update(['status' => $newStatus]);
        
        $statusText = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.lapangan.index')->with('success', "Lapangan berhasil {$statusText}");
    }
    
    public function bookingIndex()
    {
        $status = request()->input('status');
        $search = request()->input('search');
        $start = request()->input('start');
        $end = request()->input('end');
        
        $query = Booking::with(['user', 'lapangan', 'payment']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($subQ) use ($search) {
                    $subQ->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhereHas('lapangan', function($subQ) use ($search) {
                    $subQ->where('nama', 'like', '%' . $search . '%');
                });
            });
        }
        
        if ($start && $end) {
            $query->whereBetween('tanggal', [$start, $end]);
        }
        
        $bookings = $query->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'desc')->paginate(20);
        
        // Calculate statistics
        $pendingCount = Booking::where('status', 'pending')->count();
        $confirmedCount = Booking::where('status', 'confirmed')->count();
        $rejectedCount = Booking::where('status', 'rejected')->count();
        $cancelledCount = Booking::where('status', 'cancelled')->count();
        $todayCount = Booking::whereDate('tanggal', now()->toDateString())->count();
        
        return view('admin.booking.index', compact(
            'bookings',
            'pendingCount',
            'confirmedCount', 
            'rejectedCount',
            'cancelledCount',
            'todayCount',
            'status',
            'search',
            'start',
            'end'
        ));
    }
    
    public function bookingShow($id)
    {
        $booking = Booking::with(['user', 'lapangan', 'payment'])->findOrFail($id);
        return response()->json(['booking' => $booking]);
    }
    
    public function bookingConfirm($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'confirmed']);
        
        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil dikonfirmasi');
    }
    
    public function bookingReject(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'rejected',
            'catatan' => $request->input('reason', $booking->catatan)
        ]);
        
        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil ditolak');
    }
    
    public function bookingComplete($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'completed']);
        
        return redirect()->route('admin.booking.index')->with('success', 'Booking ditandai selesai');
    }
    
    public function bookingCancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        
        return redirect()->route('admin.booking.index')->with('success', 'Booking berhasil dibatalkan');
    }
    
    public function keuanganIndex()
    {
        $start = request()->input('start', now()->startOfMonth()->format('Y-m-d'));
        $end = request()->input('end', now()->format('Y-m-d'));
        
        $payments = Payment::with(['booking.user', 'booking.lapangan'])
            ->where('status', 'verified')
            ->when($start && $end, function ($q) use ($start, $end) {
                $q->whereHas('booking', function ($qb) use ($start, $end) {
                    $qb->whereBetween('tanggal', [$start, $end]);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalIncome = $payments->sum('jumlah');
        $completedBookings = Booking::where('status', 'completed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $avgTransaction = $payments->count() > 0 ? $totalIncome / $payments->count() : 0;
        
        // Payment method statistics
        $paymentStats = $payments->groupBy('metode_pembayaran')->map(function ($group) {
            return [
                'total' => $group->sum('jumlah'),
                'count' => $group->count()
            ];
        });
        
        // Revenue by lapangan
        $byLapangan = $payments->groupBy(function ($p) {
            return $p->booking->lapangan->nama ?? 'Unknown';
        })->map(function ($group) {
            return $group->sum('jumlah');
        });
        
        // Get all lapangan for filter
        $lapangans = Lapangan::all();

        return view('admin.keuangan.index', compact(
            'payments', 
            'totalIncome', 
            'completedBookings', 
            'pendingBookings', 
            'avgTransaction',
            'paymentStats',
            'byLapangan',
            'lapangans',
            'start',
            'end'
        ));
    }
    
    public function hakAksesIndex()
    {
        $users = User::withCount('bookings')->orderBy('role')->paginate(20);
        
        // Calculate statistics
        $totalUsers = User::count();
        $customerCount = User::where('role', 'customer')->count();
        $managerCount = User::where('role', 'manager')->count();
        $adminCount = User::where('role', 'admin')->count();
        $superadminCount = User::where('role', 'superadmin')->count();
        
        return view('admin.hakakses.index', compact(
            'users',
            'totalUsers',
            'customerCount',
            'managerCount',
            'adminCount',
            'superadminCount'
        ));
    }
    
    public function webSettingIndex()
    {
        // Get settings from database or config
        $settings = [
            'app_name' => config('app.name', 'Manfutsal'),
            'app_description' => 'Sistem Manajemen Futsal',
            'app_email' => config('mail.from.address', 'info@manfutsal.com'),
            'app_phone' => '+62 812-3456-7890',
            'app_address' => 'Jakarta, Indonesia',
            'social_facebook' => 'https://facebook.com/manfutsal',
            'social_instagram' => 'https://instagram.com/manfutsal',
            'social_twitter' => 'https://twitter.com/manfutsal',
            'maintenance_mode' => false,
            'allow_registration' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
            'max_booking_per_day' => 3,
            'max_booking_hours' => 4,
            'auto_confirm_booking' => false,
            'payment_timeout' => 60,
        ];
        
        return view('admin.websetting.index', compact('settings'));
    }
    
    public function webSettingUpdate(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_description' => 'required|string|max:500',
            'app_email' => 'required|email',
            'app_phone' => 'nullable|string|max:20',
            'app_address' => 'nullable|string|max:500',
            'social_facebook' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'maintenance_mode' => 'boolean',
            'allow_registration' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'max_booking_per_day' => 'required|integer|min:1|max:10',
            'max_booking_hours' => 'required|integer|min:1|max:12',
            'auto_confirm_booking' => 'boolean',
            'payment_timeout' => 'required|integer|min:15|max:180',
        ]);
        
        // In a real application, you would save these to database or config files
        // For now, we'll just return success message
        
        return redirect()->route('admin.websetting.index')->with('success', 'Pengaturan web berhasil diperbarui');
    }
}
