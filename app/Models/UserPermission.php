<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'page_name',
        'can_access',
    ];

    protected $casts = [
        'can_access' => 'boolean',
    ];

    /**
     * Get the user that owns the permission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get available pages that can be assigned
     */
    public static function getAvailablePages()
    {
        return [
            'dashboard' => 'Dashboard',
            'users' => 'Manajemen User',
            'lapangans' => 'Manajemen Lapangan',
            'bookings' => 'Manajemen Booking',
            'keuangan' => 'Laporan Keuangan',
            'activities' => 'Log Activity',
            'settings' => 'Pengaturan',
            'access' => 'Hak Akses',
        ];
    }

    /**
     * Check if user has access to specific page
     */
    public static function hasAccess($userId, $pageName)
    {
        return self::where('user_id', $userId)
            ->where('page_name', $pageName)
            ->where('can_access', true)
            ->exists();
    }

    /**
     * Grant access to page for user
     */
    public static function grantAccess($userId, $pageName)
    {
        return self::updateOrCreate(
            ['user_id' => $userId, 'page_name' => $pageName],
            ['can_access' => true]
        );
    }

    /**
     * Revoke access from page for user
     */
    public static function revokeAccess($userId, $pageName)
    {
        return self::where('user_id', $userId)
            ->where('page_name', $pageName)
            ->update(['can_access' => false]);
    }

    /**
     * Get all permissions for user
     */
    public static function getUserPermissions($userId)
    {
        return self::where('user_id', $userId)
            ->pluck('can_access', 'page_name')
            ->toArray();
    }

    /**
     * Set permissions for user (bulk update)
     */
    public static function setUserPermissions($userId, $permissions)
    {
        $availablePages = self::getAvailablePages();
        
        // Get user to check if superadmin
        $user = \App\Models\User::find($userId);
        
        foreach ($availablePages as $pageName => $pageTitle) {
            // Superadmin always has access, but we still save it for consistency
            if ($user && $user->role === 'superadmin') {
                $canAccess = true;
            } else {
                $canAccess = isset($permissions[$pageName]) && ($permissions[$pageName] === true || $permissions[$pageName] === 'true' || $permissions[$pageName] === 1);
            }
            
            self::updateOrCreate(
                ['user_id' => $userId, 'page_name' => $pageName],
                ['can_access' => $canAccess]
            );
        }
    }
}
