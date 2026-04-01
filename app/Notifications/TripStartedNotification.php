<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class TripStartedNotification extends Notification
{
    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => '🛣️ Perjalanan Dimulai',
            'message'      => "Perjalanan Anda ({$this->booking->booking_code}) ke {$this->booking->destination} telah resmi dimulai. Semoga perjalanan lancar!",
            'icon'         => 'truck',
            'color'        => 'green',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
