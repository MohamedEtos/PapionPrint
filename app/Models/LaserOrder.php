<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class LaserOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'material_id',
        'source',
        'add_ceylon',
        'height',
        'width',
        'required_pieces',
        'pieces_per_section',
        'section_count',
        'notes',
        'manufacturing_cost',
        'total_cost',
        'image_path',
        'custom_operating_cost',
    ];

    public function customer()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function material()
    {
        return $this->belongsTo(LaserMaterial::class, 'material_id');
    }
}
