<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Notifications\Notification;

class BookingRejectedNotification extends Notification
{
    public function __construct(
        private Booking $booking,
        private User    $approver,
        private ?string $note = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $noteText = $this->note ? " Catatan: \"{$this->note}\"." : '';

        return [
            'title'        => '❌ Pengajuan Ditolak',
            'message'      => "Pengajuan Anda ({$this->booking->booking_code}) ke {$this->booking->destination} ditolak oleh {$this->approver->name}.{$noteText} Anda dapat mengajukan kembali.",
            'icon'         => 'x',
            'color'        => 'red',
            'booking_id'   => $this->booking->id,
            'booking_code' => $this->booking->booking_code,
        ];
    }
}
