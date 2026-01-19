<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machines extends Model
{
    protected $fillable = [
        'name',
        'type',
        'status',
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
