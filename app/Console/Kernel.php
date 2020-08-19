<?php

namespace App\Console;

use App\Console\Commands\AuditStock;
use App\Console\Commands\AuditStockAlert;
use App\Console\Commands\CalculatePoint;
use App\Console\Commands\CalculateBenefit;
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
        AuditStock::class,
        AuditStockAlert::class,
        CalculateBenefit::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:audit-stock')->everyTenMinutes()->withoutOverlapping();
        $schedule->command('command:audit-stock-alert')->everyTenMinutes()->withoutOverlapping();
        $schedule->command('command:calculate-benefit')->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
