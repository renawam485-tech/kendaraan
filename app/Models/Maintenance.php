<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Maintenance.php

class Maintenance extends Model
{
    protected $fillable = ['vehicle_id', 'start_date', 'end_date', 'type', 'description', 'cost', 'status'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}