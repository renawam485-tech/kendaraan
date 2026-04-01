<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Prepared = 'prepared';
    case Active = 'active';
    case Completed = 'completed';
    case CancelReq = 'cancel_req';
    case Cancelled = 'cancelled';

    // Method untuk label yang mudah dibaca manusia
    public function label(): string
    {
        return match($this) {
            self::Pending => 'Menunggu Persetujuan',
            self::Approved => 'Disetujui Atasan',
            self::Rejected => 'Ditolak',
            self::Prepared => 'Unit Siap',
            self::Active => 'Sedang Jalan',
            self::Completed => 'Selesai',
            self::CancelReq => 'Pengajuan Batal',
            self::Cancelled => 'Dibatalkan',
        };
    }

    // Method untuk warna (Cocok untuk Tailwind CSS / Bootstrap)
    // Saya contohkan pakai class warna Tailwind
    public function color(): string
    {
        return match($this) {
            self::Pending => 'yellow',    // bg-yellow-100 text-yellow-800
            self::Approved => 'blue',     // bg-blue-100 text-blue-800
            self::Rejected => 'red',      // bg-red-100 text-red-800
            self::Prepared => 'purple',   // bg-purple-100 text-purple-800
            self::Active => 'green',      // bg-green-100 text-green-800
            self::Completed => 'gray',    // bg-gray-100 text-gray-800
            self::CancelReq => 'orange',  // bg-orange-100 text-orange-800
            self::Cancelled => 'red',     // bg-red-100 text-red-800
        };
    }
}