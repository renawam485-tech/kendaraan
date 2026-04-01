<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WelcomeGuideNotification extends Notification
{
    public function __construct(private string $role) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return match ($this->role) {
            'staff'    => [
                'title'   => '📋 Panduan untuk Staff',
                'message' => "(1) Klik 'Ajukan Sewa' → isi form → submit. (2) Tunggu persetujuan Atasan — Anda akan dapat notifikasi. (3) Jika disetujui, Admin GA menyiapkan kendaraan. (4) Pengajuan aktif dapat dibatalkan selama belum diproses.",
                'icon'    => 'file',
                'color'   => 'indigo',
            ],
            'approver' => [
                'title'   => '📋 Panduan untuk Approver',
                'message' => "(1) Notifikasi masuk saat ada pengajuan baru. (2) Buka 'Persetujuan' untuk melihat daftar. (3) Klik Setujui atau Tolak — sertakan catatan jika perlu. (4) Riwayat keputusan tersimpan di menu 'Riwayat'.",
                'icon'    => 'check',
                'color'   => 'green',
            ],
            'admin_ga' => [
                'title'   => '📋 Panduan untuk Admin GA',
                'message' => "(1) Booking disetujui → buka 'Persiapan'. (2) Tugaskan unit internal atau vendor eksternal. (3) Klik 'Mulai Perjalanan' saat kendaraan berangkat. (4) Monitor di 'Pantau' — tandai selesai saat kendaraan kembali. (5) Semua riwayat tersimpan di 'Riwayat Trip'. Master data dikelola di 'Unit' dan 'User'.",
                'icon'    => 'truck',
                'color'   => 'blue',
            ],
            default    => [
                'title'   => '📋 Panduan Penggunaan',
                'message' => 'Hubungi Admin GA jika membutuhkan bantuan.',
                'icon'    => 'info',
                'color'   => 'blue',
            ],
        };
    }
}
