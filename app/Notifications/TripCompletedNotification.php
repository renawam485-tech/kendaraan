<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class TripCompletedNotification extends Notification
{
    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => '✅ Perjalanan Selesai',
            'message'      => "Perjalanan Anda ({$this->booking->booking_code}) ke {$this->booking->destination} telah selesai dan tercatat dalam sistem. Terima kasih!",
            'icon'         => 'check',
            'color'        => 'green',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
