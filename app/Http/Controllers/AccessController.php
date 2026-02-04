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
            'permissions' => 'required|array',
            'permissions.*' => 'boolean',
        ]);

        $userId = $request->user_id;
        $permissions = $request->permissions;

        // Update permissions for the user
        UserPermission::setUserPermissions($userId, $permissions);

        // Log the activity
        $user = User::find($userId);
        activity()->causedBy(Auth::user())
            ->performedOn($user)
            ->log('Updated permissions for user: ' . $user->name);

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


