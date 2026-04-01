<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
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
                'title'   => '👋 Selamat Datang di Drivora!',
                'message' => 'Sebagai Staff, Anda bisa mengajukan peminjaman kendaraan melalui menu "Ajukan Sewa". Pengajuan akan diteruskan ke Atasan untuk disetujui.',
                'icon'    => 'info',
                'color'   => 'blue',
            ],
            'approver' => [
                'title'   => '👋 Selamat Datang, Approver!',
                'message' => 'Tugas utama Anda adalah meninjau pengajuan peminjaman kendaraan dari staf. Setiap ada pengajuan baru Anda akan menerima notifikasi di sini.',
                'icon'    => 'info',
                'color'   => 'indigo',
            ],
            'admin_ga' => [
                'title'   => '👋 Selamat Datang, Admin GA!',
                'message' => 'Anda mengelola seluruh operasional kendaraan — penugasan unit, koordinasi vendor, hingga pemantauan perjalanan aktif.',
                'icon'    => 'info',
                'color'   => 'blue',
            ],
            default    => [
                'title'   => '👋 Selamat Datang di Drivora!',
                'message' => 'Sistem manajemen kendaraan operasional siap membantu Anda.',
                'icon'    => 'info',
                'color'   => 'blue',
            ],
        };
    }
}
