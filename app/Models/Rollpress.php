<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rollpress extends Model
{
    //
    public function order()
    {
        return $this->belongsTo(Printers::class, 'orderId');
    }
}
