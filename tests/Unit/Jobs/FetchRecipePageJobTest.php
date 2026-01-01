<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\QueueEnum;
use App\Http\Clients\HelloFresh\HelloFreshClient;
use App\Http\Clients\HelloFresh\Responses\RecipesResponse;
use App\Jobs\Recipe\FetchRecipePageJob;
use App\Models\Country;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class FetchRecipePageJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $country = Country::factory()->create();

        $job = new FetchRecipePageJob($country, 'en');

        $this->assertInstanceOf(FetchRecipePageJob::class, $job);
    }

    #[Test]
    public function it_uses_hellofresh_queue(): void
    {
        $country = Country::factory()->create();

        $job = new FetchRecipePageJob($country, 'en');

        $this->assertSame(QueueEnum::HelloFresh->value, $job->queue);
    }

    #[Test]
    public function it_has_correct_tries(): void
    {
        $country = Country::factory()->create();

        $job = new FetchRecipePageJob($country, 'en');

        $this->assertSame(3, $job->tries);
    }

    #[Test]
    public function it_stores_country_and_locale(): void
    {
        $country = Country::factory()->create();

        $job = new FetchRecipePageJob($country, 'de');

        $this->assertTrue($job->country->is($country));
        $this->assertSame('de', $job->locale);
    }

    #[Test]
    public function it_defaults_skip_to_zero(): void
    {
        $country = Country::factory()->create();

        $job = new FetchRecipePageJob($country, 'en');

        $this->assertSame(0, $job->skip);
    }

    #[Test]
    public function it_accepts_custom_skip_value(): void
    {
        $country = Country::factory()->create();

        $job = new FetchRecipePageJob($country, 'en', 100);

        $this->assertSame(100, $job->skip);
    }

    #[Test]
    public function handle_fetches_recipes(): void
    {
        $country = Country::factory()->create();

        $response = $this->createRecipesResponse([
            'items' => [
                ['id' => 'recipe-1', 'name' => 'Recipe 1'],
            ],
            'take' => 50,
            'skip' => 0,
            'count' => 1,
            'total' => 1,
        ]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getRecipes')
            ->once()
            ->with($country, 'en', 0)
            ->andReturn($response);

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en');
        $job->handle($client);

        $this->assertTrue(true);
    }

    #[Test]
    public function handle_uses_skip_value(): void
    {
        $country = Country::factory()->create();

        $response = $this->createRecipesResponse([
            'items' => [],
            'take' => 50,
            'skip' => 100,
            'count' => 0,
            'total' => 100,
        ]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('withOutThrow')->andReturnSelf();
        $client->shouldReceive('getRecipes')
            ->once()
            ->with($country, 'en', 100)
            ->andReturn($response);

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en', 100);
        $job->handle($client);

        $this->assertTrue(true);
    }

    protected function createRecipesResponse(array $data): RecipesResponse
    {
        $psr7Response = new Psr7Response(200, [], json_encode($data));

        return new RecipesResponse($psr7Response);
    }
}
