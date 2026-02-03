<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'biometric_id',
        'name',
        'shift_start',
        'shift_end',
        'base_salary',
        'overtime_rate',
    ];

    public function attendances()
    {
        return $this->hasMany(BiometricAttendance::class, 'biometric_user_id');
    }
}
