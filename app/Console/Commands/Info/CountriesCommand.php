<?php

namespace App\Console\Commands\Info;

use App\Contracts\Commands\TableHelpersTrait;
use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:info:countries')]
class CountriesCommand extends Command
{
    use TableHelpersTrait;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:info:countries';

    /**
     * The console command description.
     */
    protected $description = 'Get information\'s about stored countries';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $infos = Country::orderBy('id')
            ->get()
            ->map(function (Country $country) {
                $locales = $country->locales;
                sort($locales);
                $data = [
                    'ID' => $country->id,
                    'code' => $country->code,
                    'country' => __('country.' . Str::upper($country->code)),
                    'locales' => implode(',', $locales),
                    'domain' => $country->domain,
                    'active' => $this->centeredTableCell($country->active ? '✅' : '❌'),
                ];

                foreach (
                    [
                        'recipes',
                        'ingredients',
                        'allergens',
                        'categories',
                        'cuisines',
                        'families',
                        'labels',
                        'menus',
                        'tags',
                        'utensils',
                    ] as $table
                ) {
                    $data = array_merge($data, [
                        $table => $this->alignRightTableCell(
                            Number::format(
                                DB::table(Str::lower($country->code) . '__' . $table)->count()
                            )
                        ),
                    ]);
                }

                return $data;
            })->toArray();

        if (!count($infos)) {
            $this->components->warn('No countries found');

            return;
        }

        $this->table(
            array_map(fn (string $key) => Str::ucfirst($key), array_keys($infos[0])),
            $infos
        );
    }
}
