<?php

namespace App\Console\Commands\Data;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data:config')]
class ConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'data:config';

    /**
     * The console command description.
     */
    protected $description = 'Store config in data folder for ide-helper.js';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        file_put_contents(
            base_path('data/config.json'),
            json_encode(config('application'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
