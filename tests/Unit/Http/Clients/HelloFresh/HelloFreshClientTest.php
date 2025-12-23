<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Clients\HelloFresh;

use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Override;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

final class HelloFreshClientTest extends TestCase
{
    use RefreshDatabase;

    private HelloFreshClient $helloFreshClient;

    private Country $country;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->helloFreshClient = new HelloFreshClient();
        $this->country = Country::factory()->create([
            'code' => 'DE',
            'domain' => 'https://www.hellofresh.de',
            'locales' => ['de', 'en'],
            'take' => 50,
        ]);
    }

    #[Override]
    protected function tearDown(): void
    {
        Cache::forget('hellofresh_api_token');
        parent::tearDown();
    }

    #[Test]
    public function it_returns_cached_token(): void
    {
        Cache::put('hellofresh_api_token', 'cached-token', 3600);

        $token = $this->helloFreshClient->getToken();

        $this->assertSame('cached-token', $token);
    }

    #[Test]
    public function it_fetches_token_from_hellofresh(): void
    {
        $html = $this->buildNextDataHtml('fresh-token', 172800);

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
        ]);

        $token = $this->helloFreshClient->getToken();

        $this->assertSame('fresh-token', $token);
    }

    #[Test]
    public function it_caches_token_with_ttl(): void
    {
        $html = $this->buildNextDataHtml('fresh-token', 172800);

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
        ]);

        $this->helloFreshClient->getToken();

        $this->assertTrue(Cache::has('hellofresh_api_token'));
        $this->assertSame('fresh-token', Cache::get('hellofresh_api_token'));
    }

    #[Test]
    public function it_does_not_cache_token_with_short_ttl(): void
    {
        $html = $this->buildNextDataHtml('short-lived-token', 86400);

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
        ]);

        $token = $this->helloFreshClient->getToken();

        $this->assertSame('short-lived-token', $token);
        $this->assertFalse(Cache::has('hellofresh_api_token'));
    }

    #[Test]
    public function it_throws_when_next_data_not_found(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find __NEXT_DATA__ script tag');

        Http::fake([
            'https://www.hellofresh.de' => Http::response('<html><body>No data</body></html>'),
        ]);

        $this->helloFreshClient->getToken();
    }

    #[Test]
    public function it_throws_when_json_is_invalid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Failed to parse __NEXT_DATA__ JSON');

        $html = '<script id="__NEXT_DATA__" type="application/json">{invalid json}</script>';

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
        ]);

        $this->helloFreshClient->getToken();
    }

    #[Test]
    public function it_throws_when_server_auth_not_found(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not find serverAuth');

        $html = '<script id="__NEXT_DATA__" type="application/json">{"props":{"pageProps":{}}}</script>';

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
        ]);

        $this->helloFreshClient->getToken();
    }

    #[Test]
    public function it_throws_when_token_or_expires_missing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing access_token or expires_in');

        $html = '<script id="__NEXT_DATA__" type="application/json">{"props":{"pageProps":{"ssrPayload":{"serverAuth":{}}}}}</script>';

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
        ]);

        $this->helloFreshClient->getToken();
    }

    #[Test]
    public function it_invalidates_token(): void
    {
        Cache::put('hellofresh_api_token', 'cached-token', 3600);

        $this->helloFreshClient->invalidateToken();

        $this->assertFalse(Cache::has('hellofresh_api_token'));
    }

    #[Test]
    public function with_out_throw_sets_throw_to_false(): void
    {
        $client = $this->helloFreshClient->withOutThrow();

        $this->assertFalse($client->throw);
        $this->assertSame($this->helloFreshClient, $client);
    }

    #[Test]
    public function it_fetches_recipes(): void
    {
        Cache::put('hellofresh_api_token', 'api-token', 3600);

        Http::fake([
            'https://www.hellofresh.de/gw/api/recipes*' => Http::response([
                'items' => [],
                'take' => 50,
                'skip' => 0,
                'count' => 0,
                'total' => 0,
            ]),
        ]);

        $response = $this->helloFreshClient->getRecipes($this->country, 'de', 0);

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'country=DE')
                && str_contains($request->url(), 'locale=de-DE')
                && str_contains($request->url(), 'take=50')
                && str_contains($request->url(), 'skip=0')
                && $request->hasHeader('Authorization', 'Bearer api-token');
        });

        $this->assertSame(0, $response->total());
    }

    #[Test]
    public function it_fetches_recipes_with_skip(): void
    {
        Cache::put('hellofresh_api_token', 'api-token', 3600);

        Http::fake([
            'https://www.hellofresh.de/gw/api/recipes*' => Http::response([
                'items' => [],
                'take' => 50,
                'skip' => 100,
                'count' => 0,
                'total' => 0,
            ]),
        ]);

        $response = $this->helloFreshClient->getRecipes($this->country, 'de', 100);

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'skip=100');
        });

        $this->assertSame(100, $response->skip());
    }

    #[Test]
    public function it_fetches_single_recipe(): void
    {
        Cache::put('hellofresh_api_token', 'api-token', 3600);

        Http::fake([
            'https://www.hellofresh.de/gw/api/recipes/recipe-123*' => Http::response([
                'id' => 'recipe-123',
                'name' => 'Test Recipe',
                'slug' => 'test-recipe',
            ]),
        ]);

        $response = $this->helloFreshClient->getRecipe($this->country, 'de', 'recipe-123');

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), '/recipes/recipe-123')
                && str_contains($request->url(), 'country=DE')
                && str_contains($request->url(), 'locale=de-DE');
        });

        $this->assertSame('recipe-123', $response->id());
        $this->assertSame('Test Recipe', $response->name());
        $this->assertSame('test-recipe', $response->slug());
    }

    #[Test]
    public function it_fetches_menus(): void
    {
        Cache::put('hellofresh_api_token', 'api-token', 3600);

        Http::fake([
            'https://www.hellofresh.de/gw/api/menus*' => Http::response([
                'items' => [],
                'take' => 200,
                'skip' => 0,
                'count' => 0,
                'total' => 0,
            ]),
        ]);

        $response = $this->helloFreshClient->getMenus($this->country, 'de');

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'country=DE')
                && str_contains($request->url(), 'locale=de-DE')
                && str_contains($request->url(), 'take=200');
        });

        $this->assertSame([], $response->items());
    }

    #[Test]
    public function it_fetches_menus_with_week(): void
    {
        Cache::put('hellofresh_api_token', 'api-token', 3600);

        Http::fake([
            'https://www.hellofresh.de/gw/api/menus*' => Http::response([
                'items' => [],
                'take' => 200,
                'skip' => 0,
                'count' => 0,
                'total' => 0,
            ]),
        ]);

        $this->helloFreshClient->getMenus($this->country, 'de', '2025-W01');

        Http::assertSent(function (Request $request): bool {
            return str_contains($request->url(), 'week=2025-W01');
        });
    }

    #[Test]
    public function it_retries_on_401_response(): void
    {
        $html = $this->buildNextDataHtml('new-token', 172800);

        Http::fake([
            'https://www.hellofresh.de' => Http::response($html),
            'https://www.hellofresh.de/gw/api/recipes*' => Http::sequence()
                ->push(null, 401)
                ->push([
                    'items' => [],
                    'take' => 50,
                    'skip' => 0,
                    'count' => 0,
                    'total' => 100,
                ]),
        ]);

        Cache::put('hellofresh_api_token', 'old-expired-token', 3600);

        $response = $this->helloFreshClient->getRecipes($this->country, 'de', 0);

        $this->assertSame(100, $response->total());
        $this->assertFalse(Cache::has('hellofresh_api_token') && Cache::get('hellofresh_api_token') === 'old-expired-token');
    }

    protected function buildNextDataHtml(string $token, int $expiresIn): string
    {
        $data = [
            'props' => [
                'pageProps' => [
                    'ssrPayload' => [
                        'serverAuth' => [
                            'access_token' => $token,
                            'expires_in' => $expiresIn,
                        ],
                    ],
                ],
            ],
        ];

        return sprintf(
            '<html><head><script id="__NEXT_DATA__" type="application/json">%s</script></head></html>',
            json_encode($data)
        );
    }
}
