<?php

namespace App\Console\Commands\Countries;

use App\Models\Country;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:countries:deactivate')]
class DeactivateCommand extends ActivateCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:countries:deactivate {countries : Comma separated ids of the countries. * for all}';

    /**
     * The console command description.
     */
    protected $description = 'Deactivated selected countries';

    protected string $question = 'Do You really want to deactivate the selected countries?';

    protected function handleCommand(): void
    {
        $this->countries->each(fn (Country $country) => $country->update(['active' => false]));
    }
}
