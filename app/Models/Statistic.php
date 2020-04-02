<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    public function getVideo()
    {
        return $this->hasOne('App\Models\Video');
    }
}
