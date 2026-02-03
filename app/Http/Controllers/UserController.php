<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        // Only admin and superadmin can access
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $query = User::query();
        
        // Filter
        if (request()->has('role') && request()->role) {
            $query->where('role', request()->role);
        }
        
        if (request()->has('status') && request()->status !== '') {
            $query->where('is_active', request()->status);
        }
        
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Stats
        $totalUsers = User::count();
        $customerCount = User::where('role', 'customer')->count();
        $staffCount = User::whereIn('role', ['manager', 'admin', 'superadmin'])->count();
        $activeCount = User::where('is_active', true)->count();
        
        return view('users.index', compact('users', 'totalUsers', 'customerCount', 'staffCount', 'activeCount'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        return view('users.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,manager,admin,superadmin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'user_created',
            'description' => 'Membuat user baru: ' . $user->name,
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function show($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:customer,manager,admin,superadmin',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
            'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active,
        ]);
        
        if ($request->password) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'user_updated',
            'description' => 'Mengupdate user: ' . $user->name,
            'ip_address' => $request->ip(),
        ]);
        
        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $user = User::findOrFail($id);
        
        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri');
        }
        
        $user->delete();
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'user_deleted',
            'description' => 'Menghapus user: ' . $user->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function toggleStatus($id)
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'user_status_toggled',
            'description' => 'Mengubah status user: ' . $user->name,
            'ip_address' => request()->ip(),
        ]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User berhasil {$status}");
    }

    public function updateRole(Request $request, $id)
    {
        // Only superadmin can update role
        if (!Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        $user = User::findOrFail($id);
        
        $request->validate([
            'role' => 'required|in:customer,manager,admin,superadmin',
        ]);
        
        $user->update(['role' => $request->role]);
        
        // Log activity
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'user_role_updated',
            'description' => 'Mengubah role user ' . $user->name . ' menjadi ' . $request->role,
            'ip_address' => request()->ip(),
        ]);
        
        return back()->with('success', 'Role berhasil diperbarui');
    }
}
