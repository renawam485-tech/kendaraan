<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use App\Notifications\WelcomeGuideNotification;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingRejectedNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\CancelledByStaffNotification;
use App\Notifications\UnitAssignedNotification;
use App\Notifications\VendorAssignedNotification;
use App\Notifications\VendorCancelledNotification;
use App\Notifications\TripStartedNotification;
use App\Notifications\TripCompletedNotification;

class NotificationService
{
    /* ══════════════════════════════════════════
       WELCOME — sekali saat login pertama kali
    ══════════════════════════════════════════ */

    public static function sendWelcome(User $user): void
    {
        // Cek apakah notif welcome sudah pernah dikirim
        $alreadySent = $user->notifications()
            ->where('type', WelcomeNotification::class)
            ->exists();

        if ($alreadySent) {
            return;
        }

        $user->notify(new WelcomeNotification($user->role));
        $user->notify(new WelcomeGuideNotification($user->role));
    }

    /* ══════════════════════════════════════════
       BOOKING
    ══════════════════════════════════════════ */

    /** Dipanggil di BookingController@store */
    public static function bookingCreated(Booking $booking): void
    {
        // Konfirmasi HANYA ke staff pemohon
        $booking->user->notify(new BookingCreatedNotification($booking));
    }

    /** Dipanggil di ApprovalController@decide (approved) */
    public static function bookingApproved(Booking $booking, User $approver): void
    {
        // Beritahu HANYA ke staff pemohon
        $booking->user->notify(new BookingApprovedNotification($booking, $approver));
    }

    /** Dipanggil di ApprovalController@decide (rejected) */
    public static function bookingRejected(Booking $booking, User $approver, ?string $note = null): void
    {
        $booking->user->notify(new BookingRejectedNotification($booking, $approver, $note));
    }

    /** Dipanggil di BookingController@cancel */
    public static function bookingCancelled(Booking $booking): void
    {
        // Konfirmasi ke staff
        $booking->user->notify(new BookingCancelledNotification($booking));

        // Beritahu approver agar tidak memproses lagi
        User::where('role', 'approver')->each(
            fn(User $approver) => $approver->notify(new CancelledByStaffNotification($booking))
        );
    }

    /* ══════════════════════════════════════════
       DISPATCH
    ══════════════════════════════════════════ */

    /** Dipanggil di AdminDispatcherController@assignInternal */
    public static function unitAssigned(Booking $booking, string $vehicleName, ?string $driverName = null): void
    {
        $booking->user->notify(new UnitAssignedNotification($booking, $vehicleName, $driverName));
    }

    /** Dipanggil di AdminDispatcherController@assignExternal */
    public static function vendorAssigned(Booking $booking, string $vendorName): void
    {
        $booking->user->notify(new VendorAssignedNotification($booking, $vendorName));
    }

    /** Dipanggil di AdminDispatcherController@confirmCancelVendor */
    public static function vendorCancelled(Booking $booking): void
    {
        $booking->user->notify(new VendorCancelledNotification($booking));
    }

    /* ══════════════════════════════════════════
       TRIP
    ══════════════════════════════════════════ */

    /** Dipanggil di AdminDispatcherController@startTrip */
    public static function tripStarted(Booking $booking): void
    {
        $booking->user->notify(new TripStartedNotification($booking));
    }

    /** Dipanggil di AdminDispatcherController@completeTrip */
    public static function tripCompleted(Booking $booking): void
    {
        $booking->user->notify(new TripCompletedNotification($booking));
    }
}