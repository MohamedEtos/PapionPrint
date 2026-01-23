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
        // 'pricePerMeterId',
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
    public function machines()
    {
        return $this->belongsTo(Machines::class, 'machineId');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'designerId');
    }
    public function user2()
    {
        return $this->belongsTo(User::class, 'operatorId');
    }
    public function printingprices()
    {
        return $this->hasOne(Printingprices::class, 'orderId');
    }
    public function ordersImgs()
    {
        return $this->hasMany(OrdersImg::class, 'orderId');
    }

    /* -------------------------------------------------------------------------- */
    /*                                   Scopes                                   */
    /* -------------------------------------------------------------------------- */

    public function scopeActive($query)
    {
        return $query->whereNot('archive', '0')->whereNull('deleted_at');
    }

    public function scopeSublimation($query)
    {
        return $query->whereHas('machines', function ($q) {
            $q->where('name', 'sublimation');
        });
    }
}
