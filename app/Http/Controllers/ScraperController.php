<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ScraperController extends Controller
{
    const URI = 'https://www.googleapis.com/youtube/v3/channels';

    public function __construct()
    {
        //
    }

    /**
     * @param string $channel
     * @return View
     */
    public function scrape(string $channel)
    {
        $content = file_get_contents(
            self::URI
            . '?part=statistics'
            . '&forUsername=' . $channel
            . '&key=' . env('YOUTUBE_API_KEY')
        );

        $content = json_decode($content, true);

        return view('scraper')->with(['content' => $content]);
    }
}
