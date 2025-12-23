<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Country;
use App\Models\Menu;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Date;

/**
 * @extends Factory<Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Track used year_week values per country to avoid duplicates.
     *
     * @var array<int, array<int, bool>>
     */
    protected static array $usedYearWeeks = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_id' => Country::factory(),
            'year_week' => fn (array $attributes): int => $this->generateUniqueYearWeek($attributes['country_id']),
            'start' => fn (array $attributes): DateTime => $this->calculateStartDate($attributes['year_week']),
        ];
    }

    /**
     * Generate a unique year_week for a given country.
     */
    protected function generateUniqueYearWeek(int $countryId): int
    {
        $maxAttempts = 100;
        $attempts = 0;

        do {
            $date = fake()->dateTimeBetween('-1 year', '+1 month');
            $year = (int) $date->format('Y');
            $week = (int) $date->format('W');
            $yearWeek = ($year * 100) + $week;
            $attempts++;
        } while (isset(self::$usedYearWeeks[$countryId][$yearWeek]) && $attempts < $maxAttempts);

        self::$usedYearWeeks[$countryId][$yearWeek] = true;

        return $yearWeek;
    }

    /**
     * Calculate the start date (Monday) for a given year_week.
     */
    protected function calculateStartDate(int $yearWeek): DateTime
    {
        $year = intdiv($yearWeek, 100);
        $week = $yearWeek % 100;

        $date = Date::now();
        $date->setISODate($year, $week, 1);

        return $date;
    }
}
