<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // Daily reminders every morning 7 AM
        $schedule->job(new \App\Jobs\SendDailyScheduleReminderJob)->dailyAt('07:00');

        // Pre-class alerts every minute (checks classes starting within 15 min)
        $schedule->job(new \App\Jobs\SendPreClassAlertJob)->everyMinute();

        // Post-class notifications every 30 minutes
        $schedule->job(new \App\Jobs\SendPostClassNotificationJob)->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
