<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Dikirim ke semua user role 'admin' (Admin GA) oleh cron
 * CheckApprovalDeadlines ketika approver tidak merespons booking
 * sebelum approval_deadline.
 */
class BookingEscalatedNotification extends Notification implements ShouldQueue
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
        $overdueMinutes = (int) $this->booking->approval_deadline->diffInMinutes(now());
        $overdueLabel   = $overdueMinutes >= 60
            ? round($overdueMinutes / 60, 1) . ' jam'
            : $overdueMinutes . ' menit';

        $urgentTag = $this->booking->is_urgent ? '[URGENT] ' : '';

        return [
            'type'              => 'booking_escalated',
            'booking_id'        => $this->booking->id,
            'booking_code'      => $this->booking->booking_code,
            'title'             => "🚨 {$urgentTag}Eskalasi: Approver Tidak Merespons",
            'message'           => sprintf(
                'Booking %s (%s → %s, berangkat %s) belum disetujui oleh %s selama %s sejak deadline %s.',
                $this->booking->booking_code,
                $this->booking->user->name,
                $this->booking->destination,
                $this->booking->start_time->format('d M Y H:i'),
                $this->booking->approver->name ?? '-',
                $overdueLabel,
                $this->booking->approval_deadline->format('H:i, d M Y'),
            ),
            'url'               => route('approvals.index'),
            'is_urgent'         => $this->booking->is_urgent,
            'requester_name'    => $this->booking->user->name,
            'approver_name'     => $this->booking->approver->name ?? '-',
            'destination'       => $this->booking->destination,
            'start_time'        => $this->booking->start_time->toIso8601String(),
            'approval_deadline' => $this->booking->approval_deadline->toIso8601String(),
            'overdue_minutes'   => $overdueMinutes,
            'escalated_reason'  => $this->booking->escalated_reason,
        ];
    }
}