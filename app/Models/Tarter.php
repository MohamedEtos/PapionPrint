<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tarter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'height',
        'width',
        'cards_count',
        'pieces_per_card',
        'machine_time',
        'image_path',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function layers()
    {
        return $this->hasMany(TarterLayer::class);
    }
}
