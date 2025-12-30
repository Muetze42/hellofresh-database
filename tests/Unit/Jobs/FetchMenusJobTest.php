<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Http\Clients\HelloFresh\Responses\MenusResponse;
use App\Jobs\FetchMenusJob;
use App\Models\Country;
use App\Models\Menu;
use App\Models\Recipe;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FetchMenusJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $country = Country::factory()->create();
        $job = new FetchMenusJob($country);

        $this->assertInstanceOf(FetchMenusJob::class, $job);
    }

    #[Test]
    public function it_uses_hellofresh_queue(): void
    {
        $country = Country::factory()->create();
        $job = new FetchMenusJob($country);

        $this->assertSame(QueueEnum::HelloFresh->value, $job->queue);
    }

    #[Test]
    public function it_has_correct_tries(): void
    {
        $country = Country::factory()->create();
        $job = new FetchMenusJob($country);

        $this->assertSame(3, $job->tries);
    }

    #[Test]
    public function it_stores_country(): void
    {
        $country = Country::factory()->create();
        $job = new FetchMenusJob($country);

        $this->assertTrue($job->country->is($country));
    }

    #[Test]
    public function handle_fetches_menus_for_multiple_weeks(): void
    {
        $country = Country::factory()->create([
            'locales' => ['en'],
        ]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getMenus')
            ->times(8)
            ->andReturn($this->createMenusResponse([]));

        $job = new FetchMenusJob($country);
        $job->handle($client);

        $this->assertTrue(true);
    }

    #[Test]
    public function handle_creates_menu_with_recipes(): void
    {
        $country = Country::factory()->create([
            'locales' => ['en'],
        ]);

        $recipe = Recipe::factory()->for($country)->create([
            'hellofresh_id' => 'recipe-123',
        ]);

        $menuData = [
            'items' => [
                [
                    'id' => 'menu-1',
                    'week' => '2025-W10',
                    'courses' => [
                        ['recipe' => ['id' => 'recipe-123']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ];

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getMenus')
            ->times(8)
            ->andReturn($this->createMenusResponse($menuData));

        $job = new FetchMenusJob($country);
        $job->handle($client);

        $this->assertDatabaseHas('menus', [
            'country_id' => $country->id,
            'year_week' => 202510,
        ]);

        $menu = $country->menus()->first();
        $this->assertInstanceOf(Menu::class, $menu);
        $this->assertTrue($menu->recipes()->where('recipe_id', $recipe->id)->exists());
    }

    #[Test]
    public function handle_skips_menu_without_matching_recipes(): void
    {
        $country = Country::factory()->create([
            'locales' => ['en'],
        ]);

        $menuData = [
            'items' => [
                [
                    'id' => 'menu-1',
                    'week' => '2025-W10',
                    'courses' => [
                        ['recipe' => ['id' => 'nonexistent-recipe']],
                    ],
                ],
            ],
            'take' => 200,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ];

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getMenus')
            ->times(8)
            ->andReturn($this->createMenusResponse($menuData));

        $job = new FetchMenusJob($country);
        $job->handle($client);

        $this->assertDatabaseMissing('menus', [
            'country_id' => $country->id,
            'year_week' => 202510,
        ]);
    }

    #[Test]
    public function handle_uses_first_locale(): void
    {
        $country = Country::factory()->create([
            'locales' => ['de', 'en'],
        ]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getMenus')
            ->withArgs(function ($countryArg, $locale): bool {
                return $locale === 'de';
            })
            ->times(8)
            ->andReturn($this->createMenusResponse([]));

        $job = new FetchMenusJob($country);
        $job->handle($client);

        $this->assertTrue(true);
    }

    #[Test]
    public function handle_falls_back_to_en_when_no_locales(): void
    {
        $country = Country::factory()->create([
            'locales' => [],
        ]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getMenus')
            ->withArgs(function ($countryArg, $locale): bool {
                return $locale === 'en';
            })
            ->times(8)
            ->andReturn($this->createMenusResponse([]));

        $job = new FetchMenusJob($country);
        $job->handle($client);

        $this->assertTrue(true);
    }

    protected function createMenusResponse(array $data): MenusResponse
    {
        if ($data === []) {
            $data = [
                'items' => [],
                'take' => 200,
                'skip' => 0,
                'count' => 0,
                'total' => 0,
            ];
        }

        $psr7Response = new Psr7Response(200, [], json_encode($data));

        return new MenusResponse($psr7Response);
    }
}
