<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that owns the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, $action)
    {
        if ($action) {
            return $query->where('action', $action);
        }
        return $query;
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($startDate) {
            return $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            return $query->where('created_at', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Log activity helper
     */
    public static function log($userId, $action, $description, $ipAddress = null)
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }
}
