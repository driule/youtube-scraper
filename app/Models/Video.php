<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['id', 'video_id', 'channel_id', 'title', 'description', 'published_at'];

    public function channel()
    {
        return $this->hasOne('App\Models\Channel');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'videos_tags', 'video_id', 'tag_id');
    }

    public function statistics()
    {
        return $this->hasMany('App\Models\Statistic');
    }
}
