<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Berjalan setiap 10 menit via Laravel Scheduler.
 *
 * Tugas:
 *  1. Cari booking status Pending yang approval_deadline sudah lewat
 *     dan belum dieskalasi (escalated_to_admin = false).
 *  2. Set escalated_to_admin = true + isi escalated_reason.
 *  3. Kirim notifikasi ke semua user role 'admin'.
 *
 * Jalankan manual:
 *   php artisan bookings:check-approval-deadlines
 *   php artisan bookings:check-approval-deadlines --dry-run
 */
class CheckApprovalDeadlines extends Command
{
    protected $signature = 'bookings:check-approval-deadlines
                            {--dry-run : Tampilkan booking yang akan dieskalasi tanpa mengubah data}';

    protected $description = 'Eskalasi booking ke admin jika approver tidak merespons sebelum deadline';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('CheckApprovalDeadlines — ' . now()->format('Y-m-d H:i:s'));

        if ($isDryRun) {
            $this->warn('[DRY-RUN] Tidak ada data yang akan diubah.');
        }

        // Ambil booking yang overduenya menggunakan scope di Booking model
        $overdueBookings = Booking::overdueApproval()
            ->with(['user', 'approver'])
            ->get();

        if ($overdueBookings->isEmpty()) {
            $this->info('Tidak ada booking yang perlu dieskalasi.');
            return self::SUCCESS;
        }

        $this->warn("Ditemukan {$overdueBookings->count()} booking melewati deadline.");

        // Ambil semua admin sekali saja untuk efisiensi
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->error('Tidak ada user dengan role admin. Notifikasi tidak dapat dikirim.');
        }

        $escalatedCount = 0;
        $failedCount    = 0;

        foreach ($overdueBookings as $booking) {
            try {
                $this->escalate($booking, $admins, $isDryRun);
                $escalatedCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $this->error("Gagal eskalasi #{$booking->booking_code}: {$e->getMessage()}");

                Log::error('CheckApprovalDeadlines: gagal eskalasi', [
                    'booking_id'   => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        $this->info("Selesai. Dieskalasi: {$escalatedCount} | Gagal: {$failedCount}");

        return self::SUCCESS;
    }

    private function escalate(Booking $booking, $admins, bool $isDryRun): void
    {
        $overdueMinutes = (int) $booking->approval_deadline->diffInMinutes(now());
        $overdueLabel   = $overdueMinutes >= 60
            ? round($overdueMinutes / 60, 1) . ' jam'
            : $overdueMinutes . ' menit';

        $reason = sprintf(
            'Deadline terlampaui %s. Deadline: %s | %s | Pemohon: %s | Tujuan: %s | Berangkat: %s',
            $overdueLabel,
            $booking->approval_deadline->format('d/m/Y H:i'),
            $booking->is_urgent ? '[URGENT]' : '[Normal]',
            $booking->user->name ?? '-',
            $booking->destination,
            $booking->start_time->format('d/m/Y H:i'),
        );

        $tag = $booking->is_urgent ? ' [URGENT]' : '';
        $this->line("→ Eskalasi{$tag}: {$booking->booking_code} | Terlambat: {$overdueLabel}");

        if ($isDryRun) {
            return;
        }

        $booking->update([
            'escalated_to_admin' => true,
            'escalated_reason'   => $reason,
        ]);

        if ($admins->isNotEmpty()) {
            NotificationService::bookingEscalated($booking, $admins);
        }
    }
}
