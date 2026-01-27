<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StrasLayer extends Model
{
    use HasFactory, SoftDeletes;

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
