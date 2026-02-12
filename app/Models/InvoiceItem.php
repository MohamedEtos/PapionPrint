<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'itemable_id', 'itemable_type', 'custom_price', 'quantity',        'custom_details',
        'sent_date',
        'sent_status',
        'unit_type', // 'meter' or 'piece'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }
}
