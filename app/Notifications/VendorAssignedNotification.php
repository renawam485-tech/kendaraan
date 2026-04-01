<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class VendorAssignedNotification extends Notification
{
    public function __construct(
        private Booking $booking,
        private string  $vendorName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title'        => '🚙 Vendor Kendaraan Dikonfirmasi',
            'message'      => "Kendaraan dari vendor {$this->vendorName} telah disiapkan untuk perjalanan Anda ({$this->booking->booking_code}) ke {$this->booking->destination}.",
            'icon'         => 'truck',
            'color'        => 'indigo',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
