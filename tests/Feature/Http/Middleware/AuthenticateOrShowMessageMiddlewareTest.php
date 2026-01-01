<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Models\Country;
use App\Models\User;
use Iterator;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthenticateOrShowMessageMiddlewareTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn (): Country => $country);
    }

    /**
     * @return Iterator<string, array{string}>
     */
    public static function protectedRoutesProvider(): Iterator
    {
        yield 'settings' => ['localized.settings'];
        yield 'lists' => ['localized.lists'];
        yield 'saved-shopping-lists' => ['localized.saved-shopping-lists'];
    }

    #[Test]
    #[DataProvider('protectedRoutesProvider')]
    public function guest_receives_401_on_protected_route(string $routeName): void
    {
        $response = $this->get(localized_route($routeName));

        $response->assertUnauthorized();
        $response->assertViewIs('web::auth.require-login');
    }

    #[Test]
    #[DataProvider('protectedRoutesProvider')]
    public function authenticated_user_can_access_protected_route(string $routeName): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(localized_route($routeName));

        $response->assertOk();
    }

    #[Test]
    public function guest_sees_login_required_message(): void
    {
        $response = $this->get(localized_route('localized.settings'));

        $response->assertUnauthorized();
        $response->assertSee(__('Login Required'));
        $response->assertSee(__('Please log in to access this page.'));
    }
}
