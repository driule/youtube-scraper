<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = ['id', 'video_id', 'views', 'likes', 'dislikes', 'favorites', 'comments'];

    public function video()
    {
        return $this->hasOne('App\Models\Video');
    }
}
