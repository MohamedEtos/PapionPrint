<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'user_id',
        'username',
        'ip_address',
        'user_agent',
        'request_url',
        'request_method',
        'message',
        'stack_trace',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
