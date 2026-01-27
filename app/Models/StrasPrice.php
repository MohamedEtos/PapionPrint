<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrasPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'size',
        'size',
        'price',
        'type', // stras, paper, global
    ];
}
