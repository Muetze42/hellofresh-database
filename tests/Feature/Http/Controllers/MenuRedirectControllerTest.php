<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\Country;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MenuRedirectControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_redirects_to_current_menu(): void
    {
        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        $currentMenu = Menu::factory()->for($country)->create([
            'year_week' => 202501,
            'start' => Date::today()->subDays(2),
        ]);

        app()->bind('current.country', fn () => $country);

        $response = $this->get('/en-US/menus');

        $response->assertRedirect();
        $response->assertRedirectContains($currentMenu->year_week);
    }

    #[Test]
    public function it_redirects_to_most_recent_past_menu_when_no_current_menu(): void
    {
        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        $pastMenu = Menu::factory()->for($country)->create([
            'year_week' => 202450,
            'start' => Date::today()->subWeeks(2),
        ]);

        app()->bind('current.country', fn () => $country);

        $response = $this->get('/en-US/menus');

        $response->assertRedirect();
        $response->assertRedirectContains((string) $pastMenu->year_week);
    }

    #[Test]
    public function it_redirects_to_first_future_menu_when_no_past_menus(): void
    {
        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        $futureMenu = Menu::factory()->for($country)->create([
            'year_week' => 202510,
            'start' => Date::today()->addWeeks(2),
        ]);

        app()->bind('current.country', fn () => $country);

        $response = $this->get('/en-US/menus');

        $response->assertRedirect();
        $response->assertRedirectContains((string) $futureMenu->year_week);
    }

    #[Test]
    public function it_returns_404_when_no_menus_exist(): void
    {
        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn () => $country);

        $response = $this->get('/en-US/menus');

        $response->assertNotFound();
    }

    #[Test]
    public function it_only_shows_menus_for_current_country(): void
    {
        $usCountry = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        $deCountry = Country::factory()->create([
            'code' => 'DE',
            'locales' => ['de'],
        ]);

        Menu::factory()->for($deCountry)->create([
            'year_week' => 202501,
            'start' => Date::today(),
        ]);

        app()->bind('current.country', fn () => $usCountry);

        $response = $this->get('/en-US/menus');

        $response->assertNotFound();
    }
}
