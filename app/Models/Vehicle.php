<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Vehicle.php

class Vehicle extends Model
{
    protected $fillable = ['name', 'license_plate', 'type', 'asset_status', 'notes'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    // Fitur: Cek apakah mobil sedang dipakai atau diservis
    public function isAvailable()
    {
        return $this->asset_status === 'available';
    }
}
