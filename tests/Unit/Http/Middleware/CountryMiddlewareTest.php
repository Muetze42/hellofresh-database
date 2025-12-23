<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\CountryMiddleware;
use App\Models\Country;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

final class CountryMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private CountryMiddleware $countryMiddleware;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->countryMiddleware = new CountryMiddleware();
    }

    #[Test]
    public function it_aborts_when_route_is_null(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $request = Request::create('/test');

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_aborts_when_locale_parameter_is_missing(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $request = Request::create('/us/en');
        $route = new Route('GET', '/{country}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'us');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_aborts_when_country_parameter_is_missing(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $request = Request::create('/us/en');
        $route = new Route('GET', '/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_aborts_when_country_code_is_not_2_characters(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $request = Request::create('/usa/en');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'usa');
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_aborts_when_country_not_found(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $request = Request::create('/xx/en');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'xx');
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_aborts_when_locale_not_in_country_locales(): void
    {
        $this->expectException(NotFoundHttpException::class);

        Country::factory()->create([
            'code' => 'us',
            'locales' => ['en'],
            'active' => true,
            'prep_min' => 5,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $request = Request::create('/us/de');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'us');
        $route->setParameter('locale', 'de');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_aborts_when_country_is_inactive(): void
    {
        $this->expectException(NotFoundHttpException::class);

        Country::factory()->inactive()->create([
            'code' => 'us',
            'locales' => ['en'],
        ]);

        $request = Request::create('/us/en');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'us');
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));
    }

    #[Test]
    public function it_sets_app_locale(): void
    {
        Country::factory()->create([
            'code' => 'de',
            'locales' => ['de', 'en'],
            'active' => true,
            'prep_min' => 5,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $request = Request::create('/de/de');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'de');
        $route->setParameter('locale', 'de');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));

        $this->assertSame('de', App::getLocale());
    }

    #[Test]
    public function it_sets_fallback_locale(): void
    {
        Country::factory()->create([
            'code' => 'ch',
            'locales' => ['de', 'fr', 'it'],
            'active' => true,
            'prep_min' => 5,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $request = Request::create('/ch/de');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'ch');
        $route->setParameter('locale', 'de');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));

        $this->assertSame('fr', App::getFallbackLocale());
    }

    #[Test]
    public function it_sets_fallback_locale_to_en_when_no_other_locales(): void
    {
        Country::factory()->create([
            'code' => 'us',
            'locales' => ['en'],
            'active' => true,
            'prep_min' => 5,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $request = Request::create('/us/en');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'us');
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));

        $this->assertSame('en', App::getFallbackLocale());
    }

    #[Test]
    public function it_binds_country_to_container(): void
    {
        $country = Country::factory()->create([
            'code' => 'us',
            'locales' => ['en'],
            'active' => true,
            'prep_min' => 5,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $request = Request::create('/us/en');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'us');
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));

        $this->assertTrue(app()->bound('current.country'));
        $this->assertTrue(resolve('current.country')->is($country));
    }

    #[Test]
    public function it_forgets_locale_and_country_parameters(): void
    {
        Country::factory()->create([
            'code' => 'us',
            'locales' => ['en'],
            'active' => true,
            'prep_min' => 5,
            'recipes_count' => 100,
            'ingredients_count' => 50,
        ]);

        $request = Request::create('/us/en');
        $route = new Route('GET', '/{country}/{locale}', fn (): string => 'OK');
        $route->bind($request);
        $route->setParameter('country', 'us');
        $route->setParameter('locale', 'en');

        $request->setRouteResolver(fn (): Route => $route);

        $this->countryMiddleware->handle($request, fn ($r): ResponseFactory|Response => response('OK'));

        $this->assertFalse($route->hasParameter('country'));
        $this->assertFalse($route->hasParameter('locale'));
    }

    #[Test]
    public function bind_country_context_sets_number_locale(): void
    {
        $country = Country::factory()->create([
            'code' => 'de',
            'locales' => ['de'],
        ]);

        $this->countryMiddleware->bindCountryContext($country, 'de');

        $this->assertSame('de', Number::defaultLocale());
    }

    #[Test]
    public function bind_country_context_can_be_called_directly(): void
    {
        $country = Country::factory()->create([
            'code' => 'fr',
            'locales' => ['fr', 'en'],
        ]);

        $this->countryMiddleware->bindCountryContext($country, 'fr');

        $this->assertSame('fr', App::getLocale());
        $this->assertSame('en', App::getFallbackLocale());
        $this->assertTrue(app()->bound('current.country'));
    }
}
