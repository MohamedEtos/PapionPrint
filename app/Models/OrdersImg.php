<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersImg extends Model
{
    protected $fillable = [
        'orderId',
        'path',
        'type',
    ];
    public function printer()
    {
        return $this->belongsTo(Printers::class, 'orderId');
    }


}
