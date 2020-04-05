<?php

namespace App\Console\Commands;

use App\Services\StatisticService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateStatsCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'update:videos-performance {id : The ID of Youtube channel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update channel videos performance statistic';

    private StatisticService $statisticService;

    public function __construct(StatisticService $statisticService)
    {
        parent::__construct();

        $this->statisticService = $statisticService;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->info("Starting...");

        $channelId = $this->argument('id');
        $this->statisticService->calculateVideosPerformance($channelId);
        $this->info("Task completed.");

        return 0;
    }
}
