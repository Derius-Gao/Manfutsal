<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'jumlah',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
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
     * Check if payment is verified
     */
    public function isVerified()
    {
        return $this->status === 'verified';
    }

    /**
     * Check if payment is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
