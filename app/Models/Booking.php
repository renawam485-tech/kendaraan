<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Traits\GeneratesBookingCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory, GeneratesBookingCode;

    protected $fillable = [
        'booking_code',
        'user_id',
        'approver_id',
        'vehicle_id',
        'driver_id',
        'start_time',
        'end_time',
        'destination',
        'purpose',
        'with_driver',
        'passenger_count',
        'preferred_vehicle_type',
        'is_rental',
        'status',
        'fulfillment_source',
        'vendor_name',
        'external_vehicle_detail',
        'cancellation_reason',
        'cancellation_fee',
        'trip_notes',
        'prepared_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'prepared_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'with_driver' => 'boolean',
        'is_rental' => 'boolean',
        'status' => BookingStatus::class,
        'cancellation_fee' => 'decimal:2',
    ];

    /**
     * Relasi ke User (Peminjam)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Approver
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Relasi ke Vehicle
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relasi ke Driver
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Relasi ke Approval Logs
     */
    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }
}