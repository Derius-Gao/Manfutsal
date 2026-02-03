<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lapangan that owns the booking.
     */
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }

    /**
     * Get the payment for the booking.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('tanggal', [$startDate, $endDate]);
        }
        if ($startDate) {
            return $query->where('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            return $query->where('tanggal', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Get duration in hours
     */
    public function getDurationInHours()
    {
        $start = \Carbon\Carbon::parse($this->jam_mulai);
        $end = \Carbon\Carbon::parse($this->jam_selesai);
        return $start->diffInHours($end);
    }

    /**
     * Check if booking can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->tanggal > now()->addDay();
    }
}
