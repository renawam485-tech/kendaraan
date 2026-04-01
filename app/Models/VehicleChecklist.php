<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/VehicleChecklist.php

class VehicleChecklist extends Model
{
    protected $guarded = ['id'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}