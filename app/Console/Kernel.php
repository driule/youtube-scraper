<?php

namespace App\Console;

use App\Console\Commands\ScrapeChannelCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ScrapeChannelCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // UCj-Qwy3Mt69VLshmM6iNy3w // my channel
        // UCibsmRkNNVPDVfCEtvnAtEw // Irena's channel
        // UC03RvJoIhm_fMwlUpm9ZvFw // Crafty Panda's channel

        // Irena's channel
        $schedule->command('scrape:channel UCibsmRkNNVPDVfCEtvnAtEw')->everyThirtyMinutes();
//        $schedule->command('scrape:channel UCibsmRkNNVPDVfCEtvnAtEw')->hourly();

        // my channel
//        $schedule->command('scrape:channel UCj-Qwy3Mt69VLshmM6iNy3w')->everyThirtyMinutes();
//        $schedule->command('scrape:channel UCj-Qwy3Mt69VLshmM6iNy3w')->everyMinute();

        // Crafty Panda's channel
        $schedule->command('scrape:channel UC03RvJoIhm_fMwlUpm9ZvFw')->everyThirtyMinutes();
//        $schedule->command('scrape:channel UC03RvJoIhm_fMwlUpm9ZvFw')->hourly();
    }
}
