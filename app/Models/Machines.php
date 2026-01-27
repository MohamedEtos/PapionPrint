<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machines extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
        'price_1_pass',
        'price_4_pass',
        'price_6_pass',
    ];
    public function printers()
    {
        return $this->hasMany(Printers::class, 'machineId');
    }
    public function printingprices()
    {
        return $this->hasMany(Printingprices::class, 'machineId');
    }
}
