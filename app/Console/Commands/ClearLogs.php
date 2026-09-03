<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will clear Laravel 12\'s logs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        file_put_contents(storage_path('logs/laravel.log'), '');
        $this->info('Log file cleared!');

        return self::SUCCESS;
    }
}
