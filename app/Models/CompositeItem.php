<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompositeItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'laser_cost' => 'decimal:2',
        'tarter_cost' => 'decimal:2',
        'print_cost' => 'decimal:2',
        'stras_cost' => 'decimal:2',
        'other_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function invoiceItem()
    {
        return $this->morphOne(InvoiceItem::class, 'itemable');
    }
}
