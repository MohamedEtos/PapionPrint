<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{

    protected $fillable = [
        'user_id',
        'title',
        'img_path',
        'body',
        'type',
        'status',
        'link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //
}
