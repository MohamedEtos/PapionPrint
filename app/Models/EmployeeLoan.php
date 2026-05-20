<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'biometric_user_id',
        'amount',
        'month',
        'year',
        'notes',
    ];

    public function biometricUser()
    {
        return $this->belongsTo(BiometricUser::class, 'biometric_user_id');
    }
}
