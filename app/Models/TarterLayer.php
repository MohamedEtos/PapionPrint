<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TarterLayer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tarter_id',
        'size', // Needle Size
        'count',
        'price',
    ];

    public function tarter()
    {
        return $this->belongsTo(Tarter::class);
    }


        public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty();
    }
}
