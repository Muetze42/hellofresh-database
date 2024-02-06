<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        $date = Carbon::now()->startOfWeek();

        return [
            'year_week' => $date->format('Y') . $date->format('W'),
            'start' => $date,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'country_id' => Country::factory(),
        ];
    }
}
