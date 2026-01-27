<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'itemable_id', 'itemable_type', 'custom_price', 'quantity'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
