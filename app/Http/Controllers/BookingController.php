<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Status yang mengunci slot kendaraan.
     */
    private const BLOCKING = [
        BookingStatus::Approved,
        BookingStatus::Prepared,
        BookingStatus::Active,
    ];

    /**
     * Jika start_time - now() <= nilai ini (menit) → booking URGENT.
     */
    private const URGENT_THRESHOLD_MINUTES = 60;

    /**
     * Deadline approver jika URGENT (menit dari waktu submit).
     */
    private const URGENT_DEADLINE_MINUTES = 30;

    /**
     * Deadline approver jika NORMAL (jam dari waktu submit).
     */
    private const NORMAL_DEADLINE_HOURS = 24;

    // =========================================================================

    public function create()
    {
        $vehicles = Vehicle::where('asset_status', 'available')->get();

        $bookings = Booking::whereIn('status', self::BLOCKING)
            ->where('end_time', '>=', now())
            ->whereNotNull('vehicle_id')
            ->select('vehicle_id', 'start_time', 'end_time')
            ->orderBy('start_time')
            ->get();

        $schedules = [];
        foreach ($bookings as $b) {
            $key = (string) $b->vehicle_id;
            $schedules[$key][] = [
                'start' => $b->start_time->toIso8601String(),
                'end'   => $b->end_time->toIso8601String(),
            ];
        }

        return view('bookings.create', compact('vehicles', 'schedules'));
    }

    // =========================================================================

    public function store(Request $request)
    {
        $request->validate([
            'start_time'   => 'required|date|after_or_equal:now',
            'end_time'     => 'required|date|after:start_time',
            'destination'  => 'required|string|max:255',
            'purpose'      => 'required|string',
            'booking_mode' => 'required|in:self,dispatch',
        ]);

        $approver = User::where('role', 'approver')->inRandomOrder()->first();

        if (! $approver) {
            return back()->with('error', 'Approver tidak ditemukan.')->withInput();
        }

        $vehicleId   = null;
        $fulfillment = 'dispatch';

        if ($request->booking_mode === 'self') {
            $request->validate(['vehicle_id' => 'required|exists:vehicles,id']);
            $vehicleId   = $request->vehicle_id;
            $fulfillment = 'internal';
        }

        // ── Hitung urgency sebelum transaction ────────────────────────────────
        $submitAt  = now();
        $startTime = Carbon::parse($request->start_time, 'Asia/Jakarta');

        // diffInMinutes(false) → signed: positif = start masih di depan
        $minutesUntilDeparture = $submitAt->diffInMinutes($startTime, false);
        $isUrgent              = $minutesUntilDeparture <= self::URGENT_THRESHOLD_MINUTES;

        $approvalDeadline = $isUrgent
            ? $submitAt->copy()->addMinutes(self::URGENT_DEADLINE_MINUTES)
            : $submitAt->copy()->addHours(self::NORMAL_DEADLINE_HOURS);
        // ──────────────────────────────────────────────────────────────────────

        $booking = DB::transaction(function () use (
            $request, $approver, $vehicleId, $fulfillment,
            $isUrgent, $approvalDeadline
        ) {
            if ($vehicleId) {
                $isConflict = Booking::where('vehicle_id', $vehicleId)
                    ->whereIn('status', self::BLOCKING)
                    ->where('start_time', '<', $request->end_time)
                    ->where('end_time',   '>', $request->start_time)
                    ->lockForUpdate()
                    ->exists();

                if ($isConflict) {
                    throw ValidationException::withMessages([
                        'vehicle_id' => 'Gagal: Kendaraan baru saja dipesan orang lain di jam tersebut.',
                    ]);
                }
            }

            return Booking::create([
                'user_id'                => Auth::id(),
                'approver_id'            => $approver->id,
                'start_time'             => $request->start_time,
                'end_time'               => $request->end_time,
                'destination'            => $request->destination,
                'purpose'                => $request->purpose,
                'with_driver'            => $request->boolean('with_driver'),
                'status'                 => BookingStatus::Pending,
                'vehicle_id'             => $vehicleId,
                'passenger_count'        => $request->passenger_count,
                'preferred_vehicle_type' => $request->boolean('is_rental') ? $request->preferred_vehicle_type : null,
                'is_rental'              => $request->boolean('is_rental'),
                'fulfillment_source'     => $request->boolean('is_rental') ? 'external' : $fulfillment,
                // ── Field urgency ─────────────────────────────────────────
                'is_urgent'              => $isUrgent,
                'approval_deadline'      => $approvalDeadline,
                'escalated_to_admin'     => false,
                'escalated_reason'       => null,
            ]);
        });

        // Notifikasi ke pemohon
        NotificationService::bookingCreated($booking);

        // Jika urgent → notifikasi khusus ke approver
        if ($isUrgent) {
            NotificationService::urgentApprovalNeeded($booking, $approver);
        }

        $message = $isUrgent
            ? '⚡ Pengajuan URGENT berhasil dikirim. Approver segera diberitahu.'
            : 'Pengajuan berhasil dikirim.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    // =========================================================================

    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'start_time' => ['required', 'date'],
            'end_time'   => ['required', 'date', 'after:start_time'],
        ]);

        $start = Carbon::parse($request->start_time);
        $end   = Carbon::parse($request->end_time);

        $conflict = Booking::where('vehicle_id', $request->vehicle_id)
            ->whereIn('status', self::BLOCKING)
            ->where('start_time', '<', $end)
            ->where('end_time',   '>', $start)
            ->exists();

        if ($conflict) {
            return response()->json([
                'available' => false,
                'message'   => 'Kendaraan ini sudah dipesan untuk rentang waktu tersebut.',
            ]);
        }

        // Informasikan jika booking ini akan berstatus URGENT
        $minutesUntilDeparture = now()->diffInMinutes($start, false);
        $willBeUrgent          = $minutesUntilDeparture <= self::URGENT_THRESHOLD_MINUTES;

        return response()->json([
            'available'      => true,
            'will_be_urgent' => $willBeUrgent,
            'urgent_message' => $willBeUrgent
                ? '⚡ Jam keberangkatan < 1 jam. Pengajuan ini akan ditandai URGENT dan approver harus merespons dalam 30 menit.'
                : null,
        ]);
    }

    // =========================================================================

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $reason = $request->input('reason', 'Dibatalkan oleh pemohon');

        DB::transaction(function () use ($booking, $reason) {
            if (in_array($booking->status, [BookingStatus::Pending, BookingStatus::Approved])) {
                $booking->update([
                    'status'              => BookingStatus::Cancelled,
                    'cancellation_reason' => $reason,
                    'cancelled_at'        => now(),
                ]);
                NotificationService::bookingCancelled($booking);

            } elseif ($booking->status === BookingStatus::Prepared) {
                if ($booking->fulfillment_source === 'external') {
                    $booking->update([
                        'status'              => BookingStatus::CancelReq,
                        'cancellation_reason' => $reason,
                    ]);
                    NotificationService::vendorCancelled($booking);
                } else {
                    $booking->update([
                        'status'              => BookingStatus::Cancelled,
                        'cancellation_reason' => $reason,
                        'cancelled_at'        => now(),
                    ]);
                    NotificationService::bookingCancelled($booking);
                }
            }
        });

        return back()->with('success', 'Status pembatalan diperbarui.');
    }

    // =========================================================================

    public function history(Request $request)
    {
        $bookingsRiwayat = $request->user()->bookings()
            ->whereIn('status', [
                BookingStatus::Completed,
                BookingStatus::Cancelled,
                BookingStatus::Rejected,
            ])
            ->latest()
            ->get();

        return view('bookings.history', compact('bookingsRiwayat'));
    }
}