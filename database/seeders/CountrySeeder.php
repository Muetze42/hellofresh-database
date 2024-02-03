<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //$countries = json_decode(file_get_contents(database_path('seeders/countries.json')), true);
        //Country::insert($countries); // Missing timestamps
        collect(json_decode(file_get_contents(database_path('seeders/countries.json')), true))
            ->each(fn (array $country) => Country::create($country));
    }
}
