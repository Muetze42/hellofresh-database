<?php

namespace App\Console\Commands\DataMaintenance;

use App\Contracts\LauncherCommandInterface;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data-maintenance:activate-all-countries')]
class ActivateAllCountriesCommand extends Command implements LauncherCommandInterface, PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:activate-all-countries';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate all countries';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Country::all()->each(function (Country $country): void {
            $country->update(['active' => true]);
        });
    }
}
