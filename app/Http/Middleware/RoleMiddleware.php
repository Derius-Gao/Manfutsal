<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support multiple roles separated by pipe (e.g., 'admin|superadmin')
        $allowedRoles = explode('|', $role);
        
        // Role hierarchy check
        $roleHierarchy = [
            'customer' => ['customer'],
            'manager' => ['manager', 'admin', 'superadmin'],
            'admin' => ['admin', 'superadmin'],
            'superadmin' => ['superadmin'],
        ];

        $hasAccess = false;
        foreach ($allowedRoles as $allowedRole) {
            $allowedRole = trim($allowedRole);
            if (isset($roleHierarchy[$allowedRole]) && in_array($user->role, $roleHierarchy[$allowedRole])) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            // Redirect to dashboard
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        }

        return $next($request);
    }
}
