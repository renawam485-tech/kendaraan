<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GeneratesBookingCode
{
    /**
     * Boot the trait.
     */
    protected static function bootGeneratesBookingCode()
    {
        static::creating(function ($model) {
            if (empty($model->booking_code)) {
                $model->booking_code = self::generateUniqueBookingCode();
            }
        });
    }

    /**
     * Generate kode booking unik dengan format: CV-YYMMDD10HASH
     * Contoh: CV-260210108F3K2Q
     * 
     * Format:
     * - CV = Car Vehicle (prefix)
     * - YYMMDD = Tanggal (Year Month Day)
     * - 10 = Kode wilayah (fixed)
     * - HASH = 6 karakter random (huruf kapital & angka)
     */
    public static function generateUniqueBookingCode(): string
    {
        do {
            // Format tanggal: YYMMDD
            $date = now()->format('ymd');
            
            // Kode wilayah (fixed)
            $regionCode = '10';
            
            // Generate hash 6 karakter (huruf kapital + angka)
            $hash = strtoupper(Str::random(6));
            
            // Format final: CV-YYMMDD10HASH
            $bookingCode = "CV-{$date}{$regionCode}{$hash}";
            
            // Cek apakah kode sudah ada di database
            $exists = self::where('booking_code', $bookingCode)->exists();
            
        } while ($exists);
        
        return $bookingCode;
    }

    /**
     * Scope untuk mencari booking berdasarkan kode
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('booking_code', $code);
    }

    /**
     * Get formatted booking code untuk display
     */
    public function getFormattedBookingCodeAttribute(): string
    {
        return $this->booking_code ?? 'N/A';
    }
}