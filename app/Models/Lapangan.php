<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'lokasi',
        'daerah',
        'kapasitas',
        'harga_per_jam',
        'fasilitas',
        'foto',
        'status',
    ];

    protected $casts = [
        'harga_per_jam' => 'decimal:2',
        'kapasitas' => 'integer',
    ];

    /**
     * Get the bookings for the lapangan.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if lapangan is available for specific date and time range
     */
    public function isAvailable($date, $startTime, $endTime)
    {
        return !$this->bookings()
            ->where('tanggal', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // Booking starts during requested time
                    $q->where('jam_mulai', '>=', $startTime)
                      ->where('jam_mulai', '<', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // Booking ends during requested time
                    $q->where('jam_selesai', '>', $startTime)
                      ->where('jam_selesai', '<=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // Booking covers entire requested time
                    $q->where('jam_mulai', '<=', $startTime)
                      ->where('jam_selesai', '>=', $endTime);
                });
            })
            ->exists();
    }

    /**
     * Scope to get only active lapangan
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope to filter by daerah
     */
    public function scopeByDaerah($query, $daerah)
    {
        if ($daerah) {
            return $query->where('daerah', $daerah);
        }
        return $query;
    }
}
