<?php

declare(strict_types=1);

namespace Tests\Unit\Livewire\Actions;

use App\Livewire\Actions\LogoutAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogoutActionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_out_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertTrue(Auth::check());

        $action = new LogoutAction();
        $response = $action();

        $this->assertFalse(Auth::check());
        $this->assertSame(200, $response->status());
    }

    #[Test]
    public function it_returns_json_success_response(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $action = new LogoutAction();
        $response = $action();

        $this->assertSame(['success' => true], $response->getData(true));
    }

    #[Test]
    public function it_invalidates_session(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Session::put('test_key', 'test_value');

        $action = new LogoutAction();
        $action();

        $this->assertNull(Session::get('test_key'));
    }

    #[Test]
    public function it_regenerates_csrf_token(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $originalToken = Session::token();

        $action = new LogoutAction();
        $action();

        $this->assertNotSame($originalToken, Session::token());
    }
}
