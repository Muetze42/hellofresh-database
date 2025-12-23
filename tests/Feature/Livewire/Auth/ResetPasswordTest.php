<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\ResetPassword;
use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::factory()->create([
            'code' => 'US',
            'locales' => ['en'],
        ]);

        app()->bind('current.country', fn (): Country => $country);
        app()->setLocale('en');
    }

    #[Test]
    public function it_can_render(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->assertStatus(200);
    }

    #[Test]
    public function it_mounts_with_token_and_email(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token', 'email' => 'test@example.com'])
            ->assertSet('token', 'test-token')
            ->assertSet('email', 'test@example.com');
    }

    #[Test]
    public function it_mounts_with_token_only(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->assertSet('token', 'test-token')
            ->assertSet('email', '');
    }

    #[Test]
    public function reset_password_validates_email(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('email', 'not-an-email')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function reset_password_validates_password(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('email', 'test@example.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function reset_password_validates_password_confirmation(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'test-token'])
            ->set('email', 'test@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'DifferentPassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function reset_password_fails_with_invalid_token(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        Livewire::test(ResetPassword::class, ['token' => 'invalid-token'])
            ->set('email', 'test@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function reset_password_succeeds_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'test@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertRedirect(localized_route('localized.recipes.index'));

        $this->assertTrue(Auth::check());
        $this->assertTrue(Hash::check('SecurePassword123!', $user->fresh()->password));
    }

    #[Test]
    public function reset_password_fails_for_unknown_email(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'some-token'])
            ->set('email', 'unknown@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->call('resetPassword')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function route_is_accessible_for_guests(): void
    {
        $response = $this->get(localized_route('localized.password.reset', ['token' => 'test-token']));

        $response->assertOk();
    }

    #[Test]
    public function route_redirects_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(localized_route('localized.password.reset', ['token' => 'test-token']));

        $response->assertRedirect();
    }
}
