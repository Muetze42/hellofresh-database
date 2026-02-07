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
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as HttpResponse;
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

        $this->assertSame(5, $job->tries);
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
    public function it_defaults_take_from_country(): void
    {
        $country = Country::factory()->create(['take' => 200]);

        $job = new FetchRecipePageJob($country, 'en');

        $this->assertSame(200, $job->take);
    }

    #[Test]
    public function it_accepts_custom_take_value(): void
    {
        $country = Country::factory()->create(['take' => 200]);

        $job = new FetchRecipePageJob($country, 'en', take: 150);

        $this->assertSame(150, $job->take);
    }

    #[Test]
    public function handle_fetches_recipes(): void
    {
        $country = Country::factory()->create(['take' => 50]);

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
        $client->shouldReceive('getRecipes')
            ->once()
            ->with($country, 'en', 0, 50)
            ->andReturn($response);

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en');
        $job->handle($client);

        $this->assertTrue(true);
    }

    #[Test]
    public function handle_uses_skip_value(): void
    {
        $country = Country::factory()->create(['take' => 50]);

        $response = $this->createRecipesResponse([
            'items' => [],
            'take' => 50,
            'skip' => 100,
            'count' => 0,
            'total' => 100,
        ]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('getRecipes')
            ->once()
            ->with($country, 'en', 100, 50)
            ->andReturn($response);

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en', 100);
        $job->handle($client);

        $this->assertTrue(true);
    }

    #[Test]
    public function handle_dispatches_new_job_with_reduced_take_on_server_error(): void
    {
        $country = Country::factory()->create(['take' => 200]);

        $httpResponse = new HttpResponse(new Psr7Response(500));
        $exception = new RequestException($httpResponse);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('getRecipes')
            ->once()
            ->andThrow($exception);

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en');
        $job->handle($client);

        Bus::assertDispatched(function (FetchRecipePageJob $dispatched) use ($country): bool {
            return $dispatched->take === 150
                && $dispatched->country->is($country)
                && $dispatched->locale === 'en'
                && $dispatched->skip === 0;
        });
    }

    #[Test]
    public function handle_does_not_dispatch_reduced_take_on_connection_error(): void
    {
        $country = Country::factory()->create(['take' => 200]);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('getRecipes')
            ->once()
            ->andThrow(new ConnectionException('Connection failed'));

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en');
        $job->handle($client);

        Bus::assertNotDispatched(FetchRecipePageJob::class);
    }

    #[Test]
    public function handle_does_not_dispatch_reduced_take_when_take_is_fifty_or_less(): void
    {
        $country = Country::factory()->create(['take' => 50]);

        $httpResponse = new HttpResponse(new Psr7Response(500));
        $exception = new RequestException($httpResponse);

        $client = Mockery::mock(HelloFreshClient::class);
        $client->shouldReceive('getRecipes')
            ->once()
            ->andThrow($exception);

        Bus::fake();

        $job = new FetchRecipePageJob($country, 'en');
        $job->handle($client);

        Bus::assertNotDispatched(FetchRecipePageJob::class);
    }

    protected function createRecipesResponse(array $data): RecipesResponse
    {
        $psr7Response = new Psr7Response(200, [], json_encode($data));

        return new RecipesResponse($psr7Response);
    }
}
