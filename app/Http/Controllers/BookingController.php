<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\User;
use App\Enums\BookingStatus;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Status yang mengunci slot kendaraan (sesuai BookingStatus enum).
     */
    private const BLOCKING = [
        BookingStatus::Approved,
        BookingStatus::Prepared,
        BookingStatus::Active,
    ];

    public function create()
    {
        $vehicles = Vehicle::where('asset_status', 'available')->get();

        /*
         * Build $schedules sebagai plain PHP array dengan EXPLICIT STRING KEY
         * agar json_encode selalu menghasilkan JSON object {"vehicleId": [...]}
         * bukan JSON array — ini penting agar JS bisa lookup dengan schedules["1"].
         *
         * Kenapa tidak pakai groupBy()->map()->values()?
         * groupBy() dengan key integer (vehicle_id) bisa menghasilkan JSON array
         * jika key-nya sequential, dan JSON object jika tidak — perilakunya tidak konsisten.
         * Dengan loop manual di bawah, hasilnya selalu konsisten.
         */
        $bookings = Booking::whereIn('status', self::BLOCKING)
            ->where('end_time', '>=', now())
            ->whereNotNull('vehicle_id')
            ->select('vehicle_id', 'start_time', 'end_time')
            ->orderBy('start_time')
            ->get();

        $schedules = [];  // akan jadi {"1": [{start, end}, ...], "3": [...]}
        foreach ($bookings as $b) {
            $key = (string) $b->vehicle_id;   // string key — wajib agar json_encode hasilkan object
            $schedules[$key][] = [
                'start' => $b->start_time->toIso8601String(),
                'end'   => $b->end_time->toIso8601String(),
            ];
        }

        return view('bookings.create', compact('vehicles', 'schedules'));
    }

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
        if (!$approver) return back()->with('error', 'Approver tidak ditemukan.')->withInput();

        $vehicleId   = null;
        $fulfillment = 'dispatch';

        if ($request->booking_mode === 'self') {
            $request->validate(['vehicle_id' => 'required|exists:vehicles,id']);
            $vehicleId   = $request->vehicle_id;
            $fulfillment = 'internal';
        }

        $booking = DB::transaction(function () use ($request, $approver, $vehicleId, $fulfillment) {

            // ── Cek konflik dengan DB lock untuk mencegah race condition ──
            if ($vehicleId) {
                $isConflict = Booking::where('vehicle_id', $vehicleId)
                    ->whereIn('status', self::BLOCKING)
                    ->where('start_time', '<', $request->end_time)
                    ->where('end_time',   '>', $request->start_time)
                    ->lockForUpdate()
                    ->exists();

                if ($isConflict) {
                    throw ValidationException::withMessages([
                        'vehicle_id' => 'Gagal: Kendaraan baru saja dipesan orang lain di jam tersebut. Silakan pilih waktu atau unit lain.',
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
            ]);
        });

        // ── Notifikasi: konfirmasi ke staff + alert ke semua approver ──
        NotificationService::bookingCreated($booking);

        return redirect()->route('dashboard')->with('success', 'Pengajuan berhasil dikirim.');
    }

    /**
     * Endpoint AJAX — cek ketersediaan kendaraan sebelum submit form.
     *
     * GET /booking/check-availability
     *   ?vehicle_id=1
     *   &start_time=2025-06-10T08:00
     *   &end_time=2025-06-10T12:00
     */
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
                'message'   => 'Kendaraan ini sudah dipesan untuk rentang waktu tersebut. '
                    . 'Silakan pilih waktu lain atau unit kendaraan yang berbeda.',
            ]);
        }

        return response()->json(['available' => true]);
    }

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

                // ── Notifikasi: konfirmasi ke staff + alert ke approver ──
                NotificationService::bookingCancelled($booking);
            } elseif ($booking->status === BookingStatus::Prepared) {

                if ($booking->fulfillment_source === 'external') {
                    $booking->update([
                        'status'              => BookingStatus::CancelReq,
                        'cancellation_reason' => $reason,
                    ]);

                    // ── Notifikasi: admin GA diberitahu vendor perlu dibatalkan ──
                    NotificationService::vendorCancelled($booking);
                } else {
                    $booking->update([
                        'status'              => BookingStatus::Cancelled,
                        'cancellation_reason' => $reason,
                        'cancelled_at'        => now(),
                    ]);

                    // ── Notifikasi: unit internal dilepas, staff dikonfirmasi ──
                    NotificationService::bookingCancelled($booking);
                }
            }
        });

        return back()->with('success', 'Status pembatalan diperbarui.');
    }

    public function history(Request $request)
{
    // Menggunakan $request->user() lebih disukai oleh IDE dibanding auth()->user()
    // sehingga garis merah 'Undefined method' akan hilang.
    
    $bookingsRiwayat = $request->user()->bookings()
        ->whereIn('status', [
            BookingStatus::Completed,
            BookingStatus::Cancelled,
            BookingStatus::Rejected,
        ])
        ->latest()
        ->get();

    // Pastikan nama view-nya 'booking.history' (tanpa 's' di kata booking)
    return view('bookings.history', compact('bookingsRiwayat'));
}
}
