<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VideoService
{
    private Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * @param string $tag
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByTag(string $tag)
    {
        return $this->video->whereHas('tags', function (Builder $query) use ($tag) {
            $query->where('name', 'LIKE', $tag . '%');
        })->get();
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->video->get();
    }
}
