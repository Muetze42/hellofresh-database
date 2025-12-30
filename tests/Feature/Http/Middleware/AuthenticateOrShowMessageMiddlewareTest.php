<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Models\Country;
use App\Models\User;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

final class AuthenticateOrShowMessageMiddlewareTest extends TestCase
{
    private Country $country;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn (): Country => $this->country);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function protectedRoutesProvider(): array
    {
        return [
            'settings' => ['localized.settings'],
            'lists' => ['localized.lists'],
            'saved-shopping-lists' => ['localized.saved-shopping-lists'],
        ];
    }

    #[Test]
    #[DataProvider('protectedRoutesProvider')]
    public function guest_receives_401_on_protected_route(string $routeName): void
    {
        $response = $this->get(localized_route($routeName));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertViewIs('auth.require-login');
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

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertSee(__('Login Required'));
        $response->assertSee(__('Please log in to access this page.'));
    }
}
