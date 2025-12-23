<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use JsonException;
use Throwable;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws JsonException
     * @throws Throwable
     */
    public function run(): void
    {
        $contents = file_get_contents(database_path('countries.json'));
        throw_if(! is_string($contents));
        $items = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        foreach ($items as $item) {
            $item['take'] = 200;
            Country::updateOrCreate(
                ['code' => $item['code']],
                Arr::except($item, ['code'])
            );
        }
    }
}
