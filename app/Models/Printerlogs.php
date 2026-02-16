<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Printerlogs extends Model
{

    use \Spatie\Activitylog\Traits\LogsActivity;

    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
        ->logAll()
        ->logOnlyDirty();
    }

}
