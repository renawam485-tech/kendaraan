<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class VendorCancelledNotification extends Notification
{
    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => 'Vendor Dibatalkan — Kendaraan Sedang Dicari',
            'message'      => "Vendor untuk perjalanan Anda ({$this->booking->booking_code}) ke {$this->booking->destination} dibatalkan. Admin sedang mencari pengganti.",
            'icon'         => 'info',
            'color'        => 'yellow',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
