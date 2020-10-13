<?php
declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Console application kernel
 */
class Kernel extends ConsoleKernel
{
    /**
     * @inheritdoc
     */
    protected $commands = [];

    /**
     * @inheritdoc
     */
    protected function schedule(Schedule $schedule): void
    {
         $schedule->command('currconv:refresh')->hourly();
    }

    /**
     * @inheritdoc
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
