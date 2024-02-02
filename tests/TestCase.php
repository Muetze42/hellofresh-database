<?php

// @codingStandardsIgnoreStart

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    public function login(User $user = null): User
    {
        $user ??= User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    public function createRequest($method, $uri): Request
    {
        $request = SymfonyRequest::create($uri, $method);

        return Request::createFromBase($request);
    }
}
