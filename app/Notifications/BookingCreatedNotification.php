<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class BookingCreatedNotification extends Notification
{
    public function __construct(private Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => 'Pengajuan Berhasil Dikirim',
            'message'      => "Pengajuan Anda ({$this->booking->booking_code}) ke {$this->booking->destination} pada " . $this->booking->departure_date?->format('d M Y') . " telah dikirim dan menunggu persetujuan Atasan.",
            'icon'         => 'file',
            'color'        => 'blue',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}