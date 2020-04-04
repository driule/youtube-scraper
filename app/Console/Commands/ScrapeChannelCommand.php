<?php

namespace App\Console\Commands;

use App\Services\VideoService;
use App\Services\YoutubeScraper;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ScrapeChannelCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'scrape:channel {id : The ID of Youtube channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape Youtube channel and save/update video statistics and tags';

    private YoutubeScraper $youtubeScraper;
    private VideoService $videoService;

    public function __construct(YoutubeScraper $youtubeScraper, VideoService $videoService)
    {
        parent::__construct();

        $this->youtubeScraper = $youtubeScraper;
        $this->videoService = $videoService;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info("Starting...");
        $channelId = $this->argument('id');

        $channelContent = $this->youtubeScraper->scrapeChannel($channelId);

        $videos = $this->youtubeScraper->scrapeVideos(
            $this->youtubeScraper->makeVideoIds($channelContent)
        );

        $this->youtubeScraper->saveChannel($channelId, $channelContent);
        $this->youtubeScraper->saveVideos($videos);

        $totalVideosScraped = count($channelContent['items']);

        while (
            $totalVideosScraped < $channelContent['pageInfo']['totalResults']
            && isset($channelContent['nextPageToken'])
        ) {
            $channelContent = $this->youtubeScraper->scrapeChannel(
                $channelId,
                $channelContent['nextPageToken']
            );
            $videos = $this->youtubeScraper->scrapeVideos(
                $this->youtubeScraper->makeVideoIds($channelContent)
            );
            $this->youtubeScraper->saveVideos($videos);
            $totalVideosScraped += count($channelContent['items']);
        }

        $this->info("Channel has been scraped. Total videos: $totalVideosScraped");

        $this->info("Updating video performance...");
        $this->videoService->updateVideosPerformance($channelId);
        $this->info("Task completed.");

        return $totalVideosScraped;
    }
}
