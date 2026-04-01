<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
{
    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => 'Pengajuan Berhasil Dibatalkan',
            'message'      => "Pengajuan {$this->booking->booking_code} ke {$this->booking->destination} telah dibatalkan. Anda dapat mengajukan kembali kapan saja.",
            'icon'         => 'info',
            'color'        => 'yellow',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
