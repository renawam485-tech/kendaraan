<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 1. Relasi: User sebagai pemohon
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // 2. Relasi: User sebagai atasan/approver
    public function approvals()
    {
        return $this->hasMany(Booking::class, 'approver_id');
    }

    // 3. Relasi: User sebagai driver
    public function assignedTrips()
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }

    // 4. Pengecekan Hak Akses Admin
    public function hasRole($role)
    {
        return $this->role === $role;
    }
}