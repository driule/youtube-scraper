<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class VideoService
{
    private Video $video;
    private Tag $tag;

    public function __construct(Video $video, Tag $tag)
    {
        $this->video = $video;
        $this->tag = $tag;
    }

    /**
     * @param string $tag
     * @return Collection
     */
    public function findByTag(string $tag)
    {
        return $this->video->whereHas('tags', function (Builder $query) use ($tag) {
            $query->where('name', 'LIKE', $tag . '%');
        })->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllTags()
    {
        return $this->tag->get(['name']);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        return $this->video->get();
    }
}
