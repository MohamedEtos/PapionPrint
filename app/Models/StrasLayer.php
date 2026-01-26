<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrasLayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'stras_id',
        'size',
        'count',
        'price',
    ];

    public function stras()
    {
        return $this->belongsTo(Stras::class, 'stras_id');
    }
}
