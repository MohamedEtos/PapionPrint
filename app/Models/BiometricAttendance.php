<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'biometric_user_id',
        'date',
        'shift_start',
        'shift_end',
        'check_in',
        'check_out',
        'status',
        'delay_minutes',
        'delay_deduction',
        'overtime_minutes',
        'overtime_pay',
        'absence_deduction',
        'is_friday',
        'is_holiday',
        'notes',
        'missing_punch',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'is_friday' => 'boolean',
        'is_holiday' => 'boolean',
    ];

    public function biometricUser()
    {
        return $this->belongsTo(BiometricUser::class, 'biometric_user_id');
    }
}
