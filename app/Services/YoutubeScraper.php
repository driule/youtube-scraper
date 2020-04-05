<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\Statistic;
use App\Models\Tag;
use App\Models\Video;

class YoutubeScraper
{
    const URI = 'https://www.googleapis.com/youtube/v3/';
    const MAX_RESULTS = 50;

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
                $this->saveVideoTags($video, $item['snippet']['tags']);
            }

            if (isset($item['statistics'])) {
                $this->saveVideoStatistics($video, $item['statistics']);
            }
        }
    }

    /**
     * @param Video $video
     * @param array $statistics
     */
    private function saveVideoStatistics(Video $video, array $statistics)
    {
        $this->statistic->create(
            [
                'video_id' => $video->id,
                'views' => $statistics['viewCount'],
                'likes' => $statistics['likeCount'],
                'dislikes' => $statistics['dislikeCount'],
                'favorites' => $statistics['favoriteCount'],
                'comments' => $statistics['commentCount'],
            ]
        );
    }

    /**
     * @param Video $video
     * @param array $tags
     */
    private function saveVideoTags(Video $video, array $tags)
    {
        foreach ($tags as $name) {
            $tag = $this->tag->where('name', $name)->first();

            if (!$tag) {
                $tag = $this->tag->create(['name' => $name]);
            }
            $video->tags()->syncWithoutDetaching($tag);
        }
    }

    /**
     * TODO: experimental code
     *
     * @param string $order {date|rating|relevance|title|videoCount|viewCount }
     *
     * This method allows us to scrape as many channels as API allows
     * Currently it shows about 1M channels available
     * In order to scrape even more channels we can variate with ordering thru $order param
     * This approach should scrape more or less all Youtube channels
     * The only problem is quota available :)
     */
    public function scrapeChannelsMassively(string $order)
    {
        $channelsContent = $this->searchChannels($order);
        $this->processChannels($channelsContent);

        $totalChannelsScrapped = count($channelsContent['items']);

        while (
            $totalChannelsScrapped < $channelsContent['pageInfo']['totalResults']
            && isset($channelsContent['nextPageToken'])
        ) {
            $channelsContent = $this->searchChannels(
                $order,
                $channelsContent['nextPageToken']
            );
            $this->processChannels($channelsContent);
            $totalChannelsScrapped += count($channelsContent['items']);
        }
    }

    /**
     * TODO: experimental code
     *
     * @param array $channelsContent
     */
    private function processChannels(array $channelsContent)
    {
        foreach ($channelsContent['items'] as $channel) {
            $channelId = $channel['snippet']['channelId'];

            // TODO: proceed with $channelId according to needs - scrape videos, statistics
        }
    }

    /**
     * TODO: experimental code
     *
     * @param string $order
     * @param string|null $pageToken
     *
     * @return array Example API response:
    {
        "kind": "youtube#searchListResponse",
        "etag": "\"xwzn9fn_LczrfK9QS3iZcGzqRGs/qHAKWpatdr_yUt4QRoXkjSx8LLA\"",
        "nextPageToken": "CAEQAA",
        "regionCode": "LT",
        "pageInfo": {
            "totalResults": 1000000,
            "resultsPerPage": N
        },
        "items": [
            {
                "kind": "youtube#searchResult",
                "etag": "\"xwzn9fn_LczrfK9QS3iZcGzqRGs/b7zszVbkH9MiAYE0IOwAHA_9HPc\"",
                "id": {
                "kind": "youtube#channel",
                "channelId": "UC2OacIzd2UxGHRGhdHl1Rhw"
            },
            "snippet": {
                "publishedAt": "2019-06-26T09:40:35.000Z",
                "channelId": "UC2OacIzd2UxGHRGhdHl1Rhw",
                "title": "早瀬 走 / Hayase Sou【にじさんじ所属】",
                "description": "にじさんじ所属のバーチャルライバー早瀬 走(ハヤセ ソウ)です！ 多趣味の陽キャです！ どうぞ皆さんよろしくお願いします。 【早瀬 走】...",
                "thumbnails": {
                    "default": {
                        "url": "https://yt3.ggpht.com/-BF5rMquYDjc/AAAAAAAAAAI/AAAAAAAAAAA/aa3jpBEKeZ8/s88-c-k-no-mo-rj-c0xffffff/photo.jpg"
                    },
                    "medium": {
                        "url": "https://yt3.ggpht.com/-BF5rMquYDjc/AAAAAAAAAAI/AAAAAAAAAAA/aa3jpBEKeZ8/s240-c-k-no-mo-rj-c0xffffff/photo.jpg"
                    },
                    "high": {
                        "url": "https://yt3.ggpht.com/-BF5rMquYDjc/AAAAAAAAAAI/AAAAAAAAAAA/aa3jpBEKeZ8/s800-c-k-no-mo-rj-c0xffffff/photo.jpg"
                    }
                },
                "channelTitle": "早瀬 走 / Hayase Sou【にじさんじ所属】",
                "liveBroadcastContent": "upcoming"
                }
            }
        ],
        ... N items
    }
    */
    private function searchChannels(string $order, string $pageToken = null)
    {
        $channelContent = file_get_contents(
            self::URI
            . 'search'
            . '?part=snippet'
            . '&maxResults=' . self::MAX_RESULTS
            . '&pageToken=' . $pageToken
            . '&order=' . $order
            . '&type=channel'
            . '&key=' . env('YOUTUBE_API_KEY')
        );

        return json_decode($channelContent, true);
    }
}
