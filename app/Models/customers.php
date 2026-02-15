<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
    ];
    public function printers()
    {
        return $this->hasMany(Printers::class,'customerId');
    }

    public function lasers()
    {
        return $this->hasMany(LaserOrder::class, 'customer_id');
    }

    public function stras()
    {
        return $this->hasMany(Stras::class, 'customerId');
    }

    public function tarters()
    {
        return $this->hasMany(Tarter::class, 'customer_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty();
    }
}
