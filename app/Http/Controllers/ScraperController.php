<?php

namespace App\Http\Controllers;

use App\Services\YoutubeScraper;
use Illuminate\View\View;

class ScraperController extends Controller
{
//    const CHANNEL_ID = 'UCj-Qwy3Mt69VLshmM6iNy3w'; // my channel
    const CHANNEL_ID = 'UCibsmRkNNVPDVfCEtvnAtEw'; // Irena's channel
//    const CHANNEL_ID = 'UC03RvJoIhm_fMwlUpm9ZvFw'; // Crafty Panda's channel

    private YoutubeScraper $youtubeScraper;

    public function __construct(YoutubeScraper $scraperService)
    {
        $this->youtubeScraper = $scraperService;
    }

    /**
     * @return View
     */
    public function scrape()
    {
        $channelContent = $this->youtubeScraper->scrapeChannel(self::CHANNEL_ID);

        $videos = $this->youtubeScraper->scrapeVideos(
            $this->youtubeScraper->makeVideoIds($channelContent)
        );

        $this->youtubeScraper->saveChannel(self::CHANNEL_ID, $channelContent);
        $this->youtubeScraper->saveVideos($videos);

        $totalVideosScraped = count($channelContent['items']);

        while (
            $totalVideosScraped < $channelContent['pageInfo']['totalResults']
            && isset($channelContent['nextPageToken'])
        ) {
            $channelContent = $this->youtubeScraper->scrapeChannel(
                self::CHANNEL_ID,
                $channelContent['nextPageToken']
            );
            $videos = $this->youtubeScraper->scrapeVideos(
                $this->youtubeScraper->makeVideoIds($channelContent)
            );
            $this->youtubeScraper->saveVideos($videos);
            $totalVideosScraped += count($channelContent['items']);
        }

        return view('scraper')->with(['content' => $videos]);
    }
}
