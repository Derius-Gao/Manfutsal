<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserPermission;

class AccessController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        $availablePages = UserPermission::getAvailablePages();

        // Ensure superadmin has all permissions in database (for consistency)
        foreach ($users as $user) {
            if ($user->role === 'superadmin') {
                $allPermissions = [];
                foreach ($availablePages as $pageKey => $pageTitle) {
                    $allPermissions[$pageKey] = true;
                }
                UserPermission::setUserPermissions($user->id, $allPermissions);
            }
        }

        // Get permissions for all users
        $userPermissions = [];
        foreach ($users as $user) {
            $userPermissions[$user->id] = UserPermission::getUserPermissions($user->id);
        }

        return view('superadmin.hak-akses.index', [
            'users' => $users,
            'availablePages' => $availablePages,
            'userPermissions' => $userPermissions,
        ]);
    }

    public function updatePermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $userId = $request->user_id;
        
        // Get user to check role
        $user = User::findOrFail($userId);
        
        // Handle both JSON string and array formats
        $permissions = $request->permissions;
        if (is_string($permissions)) {
            $permissions = json_decode($permissions, true);
        }
        
        if (!is_array($permissions)) {
            return response()->json([
                'success' => false,
                'message' => 'Format permissions tidak valid'
            ], 400);
        }

        // Update permissions for the user
        // Superadmin will automatically get all permissions set to true
        UserPermission::setUserPermissions($userId, $permissions);

        // Log the activity
        $user = User::find($userId);
        if (function_exists('activity')) {
            activity()->causedBy(Auth::user())
                ->performedOn($user)
                ->log('Updated permissions for user: ' . $user->name);
        }

        // Return JSON response for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hak akses user berhasil diperbarui'
            ]);
        }

        return redirect()->route('access.index')
            ->with('success', 'Hak akses user berhasil diperbarui');
    }

    public function getUserPermissions($userId)
    {
        $user = User::findOrFail($userId);
        $permissions = UserPermission::getUserPermissions($userId);

        return response()->json([
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }
}


