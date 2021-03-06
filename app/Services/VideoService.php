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
        })->orderBy('performance', 'desc')->get();
    }

    /**
     * @param int $performance
     * @return \Illuminate\Support\Collection
     */
    public function filterByPerformance(int $performance)
    {
        return $this->video->where('performance', '>=', $performance)
            ->orderBy('performance', 'desc')
            ->get()
        ;
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
    public function getAllVideos()
    {
        return $this->video->orderBy('performance', 'desc')->get();
    }
}
