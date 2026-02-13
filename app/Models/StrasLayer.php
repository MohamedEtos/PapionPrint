<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class StrasLayer extends Model
{
    use SoftDeletes;
    

    protected $fillable = [
        'stras_id',
        'size',
        'count',
        'price',
        'sFactory',
        'SoftDeletes',
    ];

    public function stras()
    {
        return $this->belongsTo(Stras::class, 'stras_id');
    }
}
