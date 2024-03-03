<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        if(config('app.env') !== 'production'){
            //$schedule->command('file:empty-tmp-folder')->everyMinute();
            $schedule->command('file:empty-tmp-folder')->hourly();
        }
        $schedule->command('file:empty-tmp-folder')->daily();
        $schedule->command('plan:check-expired-subscriptions')->daily();
        $schedule->command('plan:update-free-trials-status')->daily();
        $schedule->command('plan:subscription-expiring-notification')->monthlyOn(1, '08:00');
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
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
