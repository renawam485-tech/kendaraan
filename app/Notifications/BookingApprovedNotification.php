<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Notifications\Notification;

class BookingApprovedNotification extends Notification
{
    public function __construct(
        private Booking $booking,
        private User    $approver,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => '✅ Pengajuan Disetujui!',
            'message'      => "Pengajuan Anda ({$this->booking->booking_code}) ke {$this->booking->destination} telah disetujui oleh {$this->approver->name}. Admin sedang menyiapkan kendaraan.",
            'icon'         => 'check',
            'color'        => 'green',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
