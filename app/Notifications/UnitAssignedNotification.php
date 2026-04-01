<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Notifications\Notification;

class UnitAssignedNotification extends Notification
{
    public function __construct(
        private Booking $booking,
        private string  $vehicleName,
        private ?string $driverName = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $driverText = $this->driverName ? " dengan pengemudi {$this->driverName}" : '';

        return [
            'title'        => '🚗 Kendaraan Telah Disiapkan',
            'message'      => "Kendaraan {$this->vehicleName}{$driverText} telah ditugaskan untuk perjalanan Anda ({$this->booking->booking_code}) ke {$this->booking->destination}. Harap bersiap sesuai jadwal.",
            'icon'         => 'truck',
            'color'        => 'blue',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
