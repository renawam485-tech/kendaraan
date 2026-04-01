<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/ApprovalLog.php

class ApprovalLog extends Model
{
    protected $fillable = ['booking_id', 'user_id', 'action', 'comment'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
