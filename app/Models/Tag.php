<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['id', 'name'];

    public function videos()
    {
        return $this->belongsToMany('App\Models\Video', 'videos_tags', 'tag_id', 'video_id');
    }
}
