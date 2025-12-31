<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Web\Auth\AuthModal;
use App\Models\Country;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Override;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthModalTest extends TestCase
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
        Livewire::test(AuthModal::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_defaults_to_login_mode(): void
    {
        Livewire::test(AuthModal::class)
            ->assertSet('mode', 'login');
    }

    #[Test]
    public function it_can_switch_to_register_mode(): void
    {
        Livewire::test(AuthModal::class)
            ->call('switchToRegister')
            ->assertSet('mode', 'register');
    }

    #[Test]
    public function it_can_switch_to_login_mode(): void
    {
        Livewire::test(AuthModal::class)
            ->set('mode', 'register')
            ->call('switchToLogin')
            ->assertSet('mode', 'login');
    }

    #[Test]
    public function login_validates_email(): void
    {
        Livewire::test(AuthModal::class)
            ->set('email', 'not-an-email')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function login_validates_password(): void
    {
        Livewire::test(AuthModal::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        Livewire::test(AuthModal::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        Livewire::test(AuthModal::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors();

        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    #[Test]
    public function register_validates_name(): void
    {
        Livewire::test(AuthModal::class)
            ->set('name', 'X')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('acceptPrivacy', true)
            ->call('register')
            ->assertHasErrors(['name']);
    }

    #[Test]
    public function register_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        Livewire::test(AuthModal::class)
            ->set('name', 'Test User')
            ->set('email', 'existing@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('acceptPrivacy', true)
            ->call('register')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function register_validates_password_confirmation(): void
    {
        Livewire::test(AuthModal::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'DifferentPassword123!')
            ->set('acceptPrivacy', true)
            ->call('register')
            ->assertHasErrors(['password']);
    }

    #[Test]
    public function register_validates_privacy_acceptance(): void
    {
        Livewire::test(AuthModal::class)
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->set('acceptPrivacy', false)
            ->call('register')
            ->assertHasErrors(['acceptPrivacy']);
    }

    #[Test]
    public function register_creates_user_and_logs_in(): void
    {
        Livewire::test(AuthModal::class)
            ->set('name', 'New User')
            ->set('email', 'new@example.com')
            ->set('password', 'SecurePassword123!')
            ->set('password_confirmation', 'SecurePassword123!')
            ->set('acceptPrivacy', true)
            ->call('register')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'new@example.com',
        ]);

        $this->assertTrue(Auth::check());
    }

    #[Test]
    public function logout_logs_out_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertTrue(Auth::check());

        Livewire::test(AuthModal::class)
            ->call('logout')
            ->assertDispatched('user-logged-out');

        $this->assertFalse(Auth::check());
    }

    #[Test]
    public function open_for_auth_resets_form(): void
    {
        Livewire::test(AuthModal::class)
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('mode', 'register')
            ->dispatch('require-auth')
            ->assertSet('mode', 'login')
            ->assertSet('email', '')
            ->assertSet('password', '');
    }

    #[Test]
    public function login_is_rate_limited(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $component = Livewire::test(AuthModal::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrong-password');

        // Attempt 5 failed logins
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $component->call('login');
        }

        // 6th attempt should be rate limited
        $component->call('login')
            ->assertHasErrors(['email']);

        RateLimiter::clear('test@example.com|' . request()->ip());
    }

    #[Test]
    public function it_can_switch_to_forgot_password_mode(): void
    {
        Livewire::test(AuthModal::class)
            ->call('switchToForgotPassword')
            ->assertSet('mode', 'forgot-password')
            ->assertSet('resetLinkSent', false);
    }

    #[Test]
    public function switching_to_login_resets_reset_link_sent(): void
    {
        Livewire::test(AuthModal::class)
            ->set('mode', 'forgot-password')
            ->set('resetLinkSent', true)
            ->call('switchToLogin')
            ->assertSet('mode', 'login')
            ->assertSet('resetLinkSent', false);
    }

    #[Test]
    public function send_reset_link_validates_email(): void
    {
        Livewire::test(AuthModal::class)
            ->set('mode', 'forgot-password')
            ->set('email', 'not-an-email')
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function send_reset_link_fails_for_unknown_email(): void
    {
        Notification::fake();

        Livewire::test(AuthModal::class)
            ->set('mode', 'forgot-password')
            ->set('email', 'unknown@example.com')
            ->call('sendResetLink')
            ->assertHasErrors(['email']);
    }

    #[Test]
    public function send_reset_link_succeeds_for_valid_email(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        Livewire::test(AuthModal::class)
            ->set('mode', 'forgot-password')
            ->set('email', 'test@example.com')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('resetLinkSent', true);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    #[Test]
    public function open_for_auth_resets_reset_link_sent(): void
    {
        Livewire::test(AuthModal::class)
            ->set('mode', 'forgot-password')
            ->set('resetLinkSent', true)
            ->dispatch('require-auth')
            ->assertSet('mode', 'login')
            ->assertSet('resetLinkSent', false);
    }
}
