<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'ip_address',
        'device_info',
        'overtime_hours',
        'delay_minutes',
        'total_hours',
        'status_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
