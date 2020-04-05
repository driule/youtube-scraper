<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\DB;

class StatisticService
{
    private Video $video;

    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * @param string $channelId
     */
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

    /**
     * @param array $numbers
     * @return float|int
     */
    private function calculateMedian(array $numbers)
    {
        if ($numbers) {
            $count = count($numbers);
            sort($numbers);
            $middle = floor(($count - 1) / 2);
            return ($numbers[$middle] + $numbers[$middle + 1 - $count % 2]) / 2;
        }

        return 0;
    }
}
