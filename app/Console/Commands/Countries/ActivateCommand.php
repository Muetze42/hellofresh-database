<?php

namespace App\Console\Commands\Countries;

use App\Contracts\Commands\TableHelpersTrait;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:countries:activate')]
class ActivateCommand extends Command
{
    use TableHelpersTrait;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:countries:activate {countries : Comma separated ids of the countries. * for all}';

    /**
     * The console command description.
     */
    protected $description = 'Activated selected countries';

    protected string $question = 'Do You really want to activate the selected countries?';

    protected Collection $countries;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $tableData = $this->getCountriesTableData();

        if (!$this->countries->count()) {
            $this->components->error('No Countries selected');
            return;
        }

        $this->components->info('Selected countries:');
        $this->table($tableData['headers'], $tableData['rows']);

        if (!$this->confirm($this->question)) {
            return;
        }
        $this->handleCommand();

        $this->components->info('Command executed');
        $tableData = $this->getCountriesTableData();
        $this->table($tableData['headers'], $tableData['rows']);
    }

    protected function getCountriesTableData(): array
    {
        $countries = $this->argument('countries');
        $this->countries = $countries == '*' ? Country::all() :
            Country::whereIn('id', explode(',', $countries))->get();

        return [
            'headers' => ['ID', 'Code', 'Country', 'Locales', 'Active'],
            'rows' => $this->countries->map(function (Country $country) {
                $locales = $country->locales;
                sort($locales);
                return [
                    'id' => $country->getKey(),
                    'code' => $country->code,
                    'country' => __('country.' . Str::upper($country->code)),
                    'locales' => implode(',', $locales),
                    'active' => $this->centeredTableCell($country->active ? '✅' : '❌'),
                ];
            })->toArray()
        ];
    }

    protected function handleCommand(): void
    {
        $this->countries->each(fn(Country $country) => $country->update(['active' => true]));
    }
}
