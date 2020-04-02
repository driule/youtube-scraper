<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['id', 'channel_id', 'title', 'description', 'published_at'];

    public function getChannel()
    {
        return $this->hasOne('App\Models\Channel');
    }

    public function getTags()
    {
        return $this->hasMany('App\Models\Tag');
    }

    public function getStatistics()
    {
        return $this->hasMany('App\Models\Statistic');
    }
}
