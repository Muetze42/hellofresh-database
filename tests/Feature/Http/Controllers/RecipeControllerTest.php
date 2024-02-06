<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\ARecipeController;
use Tests\TestCase;

/**
 * Class RecipeControllerTest.
 *
 * @covers \App\Http\Controllers\ARecipeController
 */
final class RecipeControllerTest extends TestCase
{
    private ARecipeController $recipeController;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->recipeController = new ARecipeController();
        $this->app->instance(ARecipeController::class, $this->recipeController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->recipeController);
    }

    public function testIndex(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testStore(): void
    {
        /** @todo This test is incomplete. */
        $this->post('/path', [/* data */])
            ->assertStatus(200);
    }

    public function testShow(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testUpdate(): void
    {
        /** @todo This test is incomplete. */
        $this->put('/path', [/* data */])
            ->assertStatus(200);
    }

    public function testDestroy(): void
    {
        /** @todo This test is incomplete. */
        $this->delete('/path')
            ->assertStatus(200);
    }
}
