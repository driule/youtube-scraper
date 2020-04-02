<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Statistic;
use App\Models\Tag;
use App\Models\Video;

class YoutubeScraper
{
    const URI = 'https://www.googleapis.com/youtube/v3/';
    const MAX_RESULTS = 10;

    private Channel $channel;
    private Video $video;
    private Statistic $statistic;
    private Tag $tag;

    public function __construct(Channel $channel, Video $video, Statistic $statistic, Tag $tag)
    {
        $this->channel = $channel;
        $this->video = $video;
        $this->statistic = $statistic;
        $this->tag = $tag;
    }

    /**
     * @param string $channelId
     * @param string|null $pageToken
     *
     * @return array
     */
    public function scrapeChannel(string $channelId, string $pageToken = null)
    {
        $channelContent = file_get_contents(
            self::URI
            . 'search'
            . '?part=snippet'
            . '&channelId=' . $channelId
            . '&maxResults=' . self::MAX_RESULTS
            . '&pageToken=' . $pageToken
            . '&order=date'
            . '&type=video'
            . '&key=' . env('YOUTUBE_API_KEY')
        );

        return json_decode($channelContent, true);
    }

    /**
     * @param string $ids
     * @return array
     */
    public function scrapeVideos(string $ids)
    {
        $videos = file_get_contents(
            self::URI
            . 'videos'
            . '?part=snippet,statistics'
            . '&id=' . $ids
            . '&key=' . env('YOUTUBE_API_KEY')
        );

        return json_decode($videos, true);
    }

    /**
     * @param array $channelContent
     * @return string
     */
    public function makeVideoIds(array $channelContent)
    {
        $ids = [];
        foreach ($channelContent['items'] as $item) {
            $ids[] = $item['id']['videoId'];
        }

        return implode(',', $ids);
    }

    /**
     * @param string $channelId
     * @param array $channelContent
     */
    public function saveChannel(string $channelId, array $channelContent)
    {
        $channel = $this->channel->find($channelId);
        if (!$channel) {
            $this->channel->create(
                [
                    'id' => $channelId,
                    'title' => $channelContent['items'][0]['snippet']['channelTitle'],
                ]
            );
        }
    }

    /**
     * @param array $videoContent
     */
    public function saveVideos(array $videoContent)
    {
        foreach ($videoContent['items'] as $item) {
            $video = $this->video->where('video_id', $item['id'])->first();
            if (!$video) {
                $video = $this->video->create(
                    [
                        'video_id' => $item['id'],
                        'channel_id' => $item['snippet']['channelId'],
                        'title' => $item['snippet']['title'],
                        'description' => $item['snippet']['description'],
                    ]
                );
            }

            if (isset($item['snippet']['tags'])) {
                $this->saveTags($video, $item['snippet']['tags']);
            }

            if (isset($item['statistics'])) {
                $this->saveStatistics($video->id, $item['statistics']);
            }
        }
    }

    private function saveStatistics(string $videoId, array $statistics)
    {
        $this->statistic->create(
            [
                'video_id' => $videoId,
                'views' => $statistics['viewCount'],
                'likes' => $statistics['likeCount'],
                'dislikes' => $statistics['dislikeCount'],
                'favorites' => $statistics['favoriteCount'],
                'comments' => $statistics['commentCount'],
            ]
        );
    }

    private function saveTags(Video $video, array $tags)
    {
        foreach ($tags as $name) {
            $tag = $this->tag->where('name', $name)->first();

            if (!$tag) {
                $tag = $this->tag->create(['name' => $name]);
            }
            $video->tags()->syncWithoutDetaching($tag);
        }
    }
}
