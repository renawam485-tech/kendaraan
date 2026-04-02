<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingApprovedNotification;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingCreatedNotification;
use App\Notifications\BookingEscalatedNotification;
use App\Notifications\BookingRejectedNotification;
use App\Notifications\CancelledByStaffNotification;
use App\Notifications\TripCompletedNotification;
use App\Notifications\TripStartedNotification;
use App\Notifications\UnitAssignedNotification;
use App\Notifications\UrgentApprovalNotification;
use App\Notifications\VendorAssignedNotification;
use App\Notifications\VendorCancelledNotification;
use App\Notifications\WelcomeGuideNotification;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /* ══════════════════════════════════════════════════════════
       WELCOME
    ══════════════════════════════════════════════════════════ */

    public static function sendWelcome(User $user): void
    {
        $alreadySent = $user->notifications()
            ->where('type', WelcomeNotification::class)
            ->exists();

        if ($alreadySent) {
            return;
        }

        $user->notify(new WelcomeNotification($user->role));
        $user->notify(new WelcomeGuideNotification($user->role));
    }

    /* ══════════════════════════════════════════════════════════
       BOOKING
    ══════════════════════════════════════════════════════════ */

    /** Dipanggil di BookingController@store */
    public static function bookingCreated(Booking $booking): void
    {
        $booking->user->notify(new BookingCreatedNotification($booking));
    }

    /** Dipanggil di ApprovalController@decide (approved) */
    public static function bookingApproved(Booking $booking, User $approver): void
    {
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
        $booking->user->notify(new BookingCancelledNotification($booking));

        User::where('role', 'approver')->each(
            fn(User $approver) => $approver->notify(new CancelledByStaffNotification($booking))
        );
    }

    /* ══════════════════════════════════════════════════════════
       URGENCY & ESKALASI  ← BARU
    ══════════════════════════════════════════════════════════ */

    /**
     * Dipanggil di BookingController@store ketika is_urgent = true.
     * Mengirim notif "URGENT" ke approver agar segera merespons dalam 30 menit.
     */
    public static function urgentApprovalNeeded(Booking $booking, User $approver): void
    {
        $approver->notify(new UrgentApprovalNotification($booking));
    }

    /**
     * Dipanggil oleh Command CheckApprovalDeadlines.
     * Mengirim notif ke semua admin GA bahwa approver tidak merespons.
     *
     * @param  Collection<int, User>  $admins
     */
    public static function bookingEscalated(Booking $booking, Collection $admins): void
    {
        foreach ($admins as $admin) {
            $admin->notify(new BookingEscalatedNotification($booking));
        }
    }

    /* ══════════════════════════════════════════════════════════
       DISPATCH
    ══════════════════════════════════════════════════════════ */

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

    /* ══════════════════════════════════════════════════════════
       TRIP
    ══════════════════════════════════════════════════════════ */

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