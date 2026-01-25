<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rollpress extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'orderId', // Keeping orderId just in case, but we are moving to customerId
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
