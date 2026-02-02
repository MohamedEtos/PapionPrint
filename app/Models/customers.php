<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];
    public function printers()
    {
        return $this->hasMany(Printers::class,'customerId');
    }

    public function rollpresses()
    {
        return $this->hasMany(Rollpress::class, 'customerId');
    }
}
