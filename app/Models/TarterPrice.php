<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarterPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'size',
        'price',
        'type', // needle, paper, global, machine_time_cost
    ];
}
