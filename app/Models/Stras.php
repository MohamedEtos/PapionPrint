<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stras extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'orderId',
        'customerId',
        'height',
        'width',
        'cards_count',
        'pieces_per_card',
        'image_path',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(Printers::class, 'orderId');
    }

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customerId');
    }

    public function layers()
    {
        return $this->hasMany(StrasLayer::class, 'stras_id');
    }
}
