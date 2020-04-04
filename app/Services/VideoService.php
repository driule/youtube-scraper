<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

    public function updateVideosPerformance(string $channelId)
    {
        $videos = $this->video->where('channel_id', $channelId)->get();

        $channelFirstHourViews = [];
        foreach ($videos as $video) {
            $firstHourViews = $this->getFirstHourViews($video);
            $video->performance = $firstHourViews;
            $channelFirstHourViews[] = $firstHourViews;
        }

        $channelMedian = $this->calculateMedian($channelFirstHourViews);
        if ($channelMedian > 0) {
            foreach ($videos as $video) {
                $video->performance /= $channelMedian;
                $video->save();
            }
        }
    }

    /**
     * @param Video $video
     * @return int
     */
    private function getFirstHourViews(Video $video)
    {
        $statistics = DB::select(
            DB::raw(
                "SELECT * FROM statistics WHERE created_at <= DATE_ADD(:extended_date, INTERVAL 1 HOUR) AND video_id = :video_id ORDER BY views"
            ), array(
                'extended_date' => $video->created_at,
                'video_id' => $video->id,
            )
        );

        if (count($statistics) > 1) {
            return $statistics[count($statistics) - 1]->views - $statistics[0]->views;
        }

        return 0;
    }

    private function calculateMedian($array)
    {
        if ($array) {
            $count = count($array);
            sort($array);
            $middle = floor(($count - 1) / 2);
            return ($array[$middle] + $array[$middle + 1 - $count % 2]) / 2;
        }

        return 0;
    }
}
