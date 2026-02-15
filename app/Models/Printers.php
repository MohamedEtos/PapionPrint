<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printers extends Model
{
    use SoftDeletes, \Spatie\Activitylog\Traits\LogsActivity;
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
        'fabric_type',
        'archive',
        'manufacturing_cost',
        'timeEndOpration',
    ];
    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customerId');
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
    public function rollpress()
    {
        return $this->hasOne(Rollpress::class, 'orderId');
    }

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty();
    }
}
