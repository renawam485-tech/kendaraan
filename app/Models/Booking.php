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
        // Field urgency (ditambah via migration)
        'is_urgent',
        'approval_deadline',
        'escalated_to_admin',
        'escalated_reason',
        // Field lainnya
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
        'start_time'         => 'datetime',
        'end_time'           => 'datetime',
        'prepared_at'        => 'datetime',
        'started_at'         => 'datetime',
        'completed_at'       => 'datetime',
        'cancelled_at'       => 'datetime',
        'approval_deadline'  => 'datetime',
        'with_driver'        => 'boolean',
        'is_rental'          => 'boolean',
        'is_urgent'          => 'boolean',
        'escalated_to_admin' => 'boolean',
        'status'             => BookingStatus::class,
        'cancellation_fee'   => 'decimal:2',
    ];

    // =========================================================================
    // RELASI
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function approvalLogs(): HasMany
    {
        return $this->hasMany(ApprovalLog::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('start_time', [$startDate, $endDate]);
    }

    /**
     * Booking urgent yang masih menunggu approval.
     * Dipakai oleh DashboardController untuk tabel admin.
     */
    public function scopeUrgentPending($query)
    {
        return $query->where('is_urgent', true)
                     ->where('status', BookingStatus::Pending);
    }

    /**
     * Booking yang sudah melewati approval_deadline
     * tapi belum dieskalasi ke admin.
     * Dipakai eksklusif oleh Command CheckApprovalDeadlines.
     */
    public function scopeOverdueApproval($query)
    {
        return $query->where('status', BookingStatus::Pending)
                     ->where('escalated_to_admin', false)
                     ->whereNotNull('approval_deadline')
                     ->where('approval_deadline', '<', now());
    }
}