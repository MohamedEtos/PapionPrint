<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customers extends Model
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
}
