<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    public function index()
    {
        $query = Lapangan::query();
        
        // Filter
        if (request()->has('daerah') && request()->daerah) {
            $query->where('daerah', request()->daerah);
        }
        
        if (request()->has('status') && request()->status) {
            $query->where('status', request()->status);
        }
        
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('lokasi', 'like', '%' . $search . '%');
            });
        }
        
        // Customer hanya lihat yang aktif
        if (Auth::user()->isCustomer()) {
            $query->where('status', 'aktif');
        }
        
        $lapangans = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Stats (hanya untuk admin)
        $totalLapangan = null;
        $activeLapangan = null;
        $inactiveLapangan = null;
        $avgPrice = null;
        $daerahList = [];
        
        if (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()) {
            $totalLapangan = Lapangan::count();
            $activeLapangan = Lapangan::where('status', 'aktif')->count();
            $inactiveLapangan = Lapangan::where('status', 'tidak_aktif')->count();
            $avgPrice = Lapangan::avg('harga_per_jam');
            $daerahList = Lapangan::distinct('daerah')->pluck('daerah')->filter()->toArray();
        }
        
        return view('lapangans.index', compact('lapangans', 'totalLapangan', 'activeLapangan', 'inactiveLapangan', 'avgPrice', 'daerahList'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        return view('lapangans.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'harga_per_jam' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
            'foto' => 'nullable|image|max:2048',
        ]);
        
        $lapangan = Lapangan::create([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'daerah' => $request->daerah,
            'kapasitas' => $request->kapasitas,
            'harga_per_jam' => $request->harga_per_jam,
            'fasilitas' => $request->fasilitas,
            'status' => $request->status,
        ]);
        
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('lapangan', 'public');
            $lapangan->update(['foto' => $path]);
        }
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'lapangan_created',
            'description' => 'Membuat lapangan baru: ' . $lapangan->nama,
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->route('lapangans.index')->with('success', 'Lapangan berhasil ditambahkan');
    }

    public function show($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        
        if (request()->wantsJson()) {
            return response()->json(['lapangan' => $lapangan]);
        }
        
        return view('lapangans.show', compact('lapangan'));
    }

    public function edit($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $lapangan = Lapangan::findOrFail($id);
        return view('lapangans.edit', compact('lapangan'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $lapangan = Lapangan::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'daerah' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'harga_per_jam' => 'required|numeric|min:0',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif',
            'foto' => 'nullable|image|max:2048',
        ]);
        
        $lapangan->update([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'daerah' => $request->daerah,
            'kapasitas' => $request->kapasitas,
            'harga_per_jam' => $request->harga_per_jam,
            'fasilitas' => $request->fasilitas,
            'status' => $request->status,
        ]);
        
        if ($request->hasFile('foto')) {
            if ($lapangan->foto) {
                Storage::disk('public')->delete($lapangan->foto);
            }
            $path = $request->file('foto')->store('lapangan', 'public');
            $lapangan->update(['foto' => $path]);
        }
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'lapangan_updated',
            'description' => 'Mengupdate lapangan: ' . $lapangan->nama,
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->route('lapangans.index')->with('success', 'Lapangan berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $lapangan = Lapangan::findOrFail($id);
        
        if ($lapangan->foto) {
            Storage::disk('public')->delete($lapangan->foto);
        }
        
        $lapangan->delete();
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'lapangan_deleted',
            'description' => 'Menghapus lapangan: ' . $lapangan->nama,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('lapangans.index')->with('success', 'Lapangan berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $lapangan = Lapangan::findOrFail($id);
        $newStatus = $lapangan->status === 'aktif' ? 'tidak_aktif' : 'aktif';
        $lapangan->update(['status' => $newStatus]);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'lapangan_status_toggled',
            'description' => 'Mengubah status lapangan: ' . $lapangan->nama,
            'ip_address' => request()->ip(),
        ]);
        
        $statusText = $newStatus === 'aktif' ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Lapangan berhasil {$statusText}");
    }
}
