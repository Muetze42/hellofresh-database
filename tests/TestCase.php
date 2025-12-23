<?php

declare(strict_types=1);

// phpcs:ignoreFile

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Override;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Setup the test environment.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->setUpFaker();
        parent::setUp();
    }

    public function login(?User $user = null): User
    {
        $user ??= User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    public function createRequest(string $method, string $uri): Request
    {
        $request = SymfonyRequest::create($uri, $method);

        return Request::createFromBase($request);
    }
}
