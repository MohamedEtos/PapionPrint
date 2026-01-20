<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Printingprices extends Model
{
    protected $fillable = [
        'machineId',
        'orderId',
        'pricePerMeter',
        'totalPrice',
        'discount',
        'finalPrice',
    ];
    public function machines()
    {
        return $this->belongsTo(Machines::class, 'machineId');
    }
    public function printers()
    {
        return $this->belongsTo(Printers::class, 'orderId');
    }
}
