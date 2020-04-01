<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ScraperController extends Controller
{
    const URI = 'https://www.googleapis.com/youtube/v3/';
    const MAX_RESULTS = 1;

    const CHANNEL_ID = 'UCj-Qwy3Mt69VLshmM6iNy3w'; // my channel
    //const CHANNEL_ID = 'UCibsmRkNNVPDVfCEtvnAtEw'; // Irena's channel
    //const CHANNEL_ID = 'UC03RvJoIhm_fMwlUpm9ZvFw'; // Crafty Panda's channel

    public function __construct()
    {
        //
    }

    /**
     * @return View
     */
    public function scrape()
    {
        $channelContent = $this->scrapeChannel();
        $videos = $this->scrapeVideos($channelContent);
        $totalVideosScraped = count($channelContent['items']);

        while ($totalVideosScraped < $channelContent['pageInfo']['totalResults']) {
            $channelContent = $this->scrapeChannel($channelContent['nextPageToken']);
            $videos = array_merge($videos, $this->scrapeVideos($channelContent));
            $totalVideosScraped += count($channelContent['items']);
        }

        return view('scraper')->with(['content' => $videos]);
    }

    /**
     * @param null $pageToken
     * @return array
     */
    private function scrapeChannel($pageToken = null)
    {
        $channelContent = file_get_contents(
            self::URI
            . 'search'
            . '?part=snippet'
            . '&channelId=' . self::CHANNEL_ID
            . '&maxResults=' . self::MAX_RESULTS
            . '&pageToken=' . $pageToken
            . '&key=' . env('YOUTUBE_API_KEY')
        );

        return json_decode($channelContent, true);
    }

    /**
     * @param array $channelContent
     * @return array
     */
    private function scrapeVideos(array $channelContent)
    {
        $videos = [];
        foreach ($channelContent['items'] as $item) {
            $video = $this->scrapeVideo($item['id']['videoId']);
            echo "{{$item['id']['videoId']}} {{$video['items'][0]['snippet']['title']}}<br/><br/>";
            $videos[] = $video;
        }

        return $videos;
    }

    /**
     * @param string $id
     * @return array
     */
    private function scrapeVideo(string $id)
    {
        $video = file_get_contents(
            self::URI
            . 'videos'
            . '?part=snippet'
            . '&id=' . $id
            . '&key=' . env('YOUTUBE_API_KEY')
        );

        return json_decode($video, true);
    }
}
