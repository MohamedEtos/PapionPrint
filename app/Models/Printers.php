<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printers extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'orderNumber',
        'customerId',
        'machineId',
        'fileHeight',
        'fileWidth',
        'fileCopies',
        'picInCopies',
        'pass',
        'meters',
        'pricePerMeterId',
        'totalPrice',
        'status',
        'paymentStatus',
        'designerId',
        'operatorId',
        'notes',
        'archive',
        'timeEndOpration',
    ];
    public function customers()
    {
        return $this->belongsTo(customers::class, 'customerId');
    }
}
