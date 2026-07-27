<?php

namespace App\Console;

use App\Console\Commands\UpdateClaudeDocs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateClaudeDocs::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        // Define scheduled tasks here if needed
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
