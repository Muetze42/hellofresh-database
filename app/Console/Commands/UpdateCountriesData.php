<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:update-countries-data')]
class UpdateCountriesData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:update-countries-data';

    /**
     * The console command description.
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        //
    }
}
