<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceArchive extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'order_id', 'order_type', 'quantity', 'unit_price', 
        'total_price', 'sent_date', 'sent_status', 'customer_name', 'invoice_id'
    ];

    public function itemable()
    {
        return $this->morphTo('itemable', 'order_type', 'order_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    
}
