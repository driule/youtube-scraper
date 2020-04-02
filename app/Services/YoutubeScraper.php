<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Video;

class YoutubeScraper
{
    const URI = 'https://www.googleapis.com/youtube/v3/';
    const MAX_RESULTS = 10;

    private Channel $channel;
    private Video $video;

    public function __construct(Channel $channel, Video $video)
    {
        $this->channel = $channel;
        $this->video = $video;
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
            $video = $this->video->find($item['id']);
            if (!$video) {
                $this->video->create(
                    [
                        'id' => $item['id'],
                        'channel_id' => $item['snippet']['channelId'],
                        'title' => $item['snippet']['title'],
                        'description' => $item['snippet']['description'],
                    ]
                );
            }
        }
    }
}
