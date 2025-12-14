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
        // Для тестирования: отправка статистики каждую минуту
        $schedule->command('statistics:daily')->everyMinute();
        
        // Для продакшена: отправка статистики каждый день в 23:59
        // $schedule->command('statistics:daily')
        //     ->dailyAt('23:59')
        //     ->timezone('Europe/Moscow');
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
