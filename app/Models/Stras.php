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
        'fabrictype',
        'fabricsrc',
        'fabriccode',
        'fabricwidth',
        'meters',
        'status',
        'paymentstatus',
        'papyershild',
        'price',
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
}
