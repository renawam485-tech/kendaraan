<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Dikirim ke APPROVER saat booking baru dibuat dan berstatus URGENT
 * (jam berangkat ≤ 1 jam dari waktu submit).
 *
 * Approver harus merespons dalam 30 menit sebelum sistem mengeskalasi
 * ke admin GA via cron CheckApprovalDeadlines.
 */
class UrgentApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Booking $booking
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $deadline = $this->booking->approval_deadline;

        return [
            'type'              => 'urgent_approval',
            'booking_id'        => $this->booking->id,
            'booking_code'      => $this->booking->booking_code,
            'title'             => '⚡ [URGENT] Permohonan Mendesak Menunggu Persetujuan Anda',
            'message'           => sprintf(
                '%s membutuhkan kendaraan ke %s pukul %s — berangkat kurang dari 1 jam! '
                . 'Anda punya waktu hingga %s untuk merespons.',
                $this->booking->user->name,
                $this->booking->destination,
                $this->booking->start_time->format('H:i'),
                $deadline?->format('H:i, d M Y') ?? '-',
            ),
            'url'               => route('approvals.index'),
            'is_urgent'         => true,
            'approval_deadline' => $deadline?->toIso8601String(),
            'start_time'        => $this->booking->start_time->toIso8601String(),
            'destination'       => $this->booking->destination,
            'requester_name'    => $this->booking->user->name,
            'purpose'           => $this->booking->purpose,
        ];
    }
}