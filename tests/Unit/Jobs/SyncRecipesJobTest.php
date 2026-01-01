<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\QueueEnum;
use App\Jobs\Recipe\FetchRecipePageJob;
use App\Jobs\Recipe\SyncRecipesJob;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SyncRecipesJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $job = new SyncRecipesJob();

        $this->assertInstanceOf(SyncRecipesJob::class, $job);
    }

    #[Test]
    public function it_uses_hellofresh_queue(): void
    {
        $job = new SyncRecipesJob();

        $this->assertSame(QueueEnum::HelloFresh->value, $job->queue);
    }

    #[Test]
    public function it_dispatches_fetch_jobs_for_each_country_locale(): void
    {
        Bus::fake();

        Country::factory()->create([
            'locales' => ['en', 'de'],
        ]);
        Country::factory()->create([
            'locales' => ['fr'],
        ]);

        $job = new SyncRecipesJob();
        $job->handle();

        Bus::assertBatched(function ($batch): bool {
            return $batch->jobs->count() === 3;
        });
    }

    #[Test]
    public function it_creates_fetch_jobs_with_correct_country_and_locale(): void
    {
        Bus::fake();

        $country = Country::factory()->create([
            'locales' => ['en'],
        ]);

        $job = new SyncRecipesJob();
        $job->handle();

        Bus::assertBatched(function ($batch) use ($country): bool {
            $firstJob = $batch->jobs->first();

            return $firstJob instanceof FetchRecipePageJob
                && $firstJob->country->is($country)
                && $firstJob->locale === 'en';
        });
    }

    #[Test]
    public function it_names_the_batch_correctly(): void
    {
        Bus::fake();

        Country::factory()->create(['locales' => ['en']]);

        $job = new SyncRecipesJob();
        $job->handle();

        Bus::assertBatched(function ($batch): bool {
            return $batch->name === 'Sync HelloFresh Recipes';
        });
    }

    #[Test]
    public function it_uses_correct_queue_for_batch(): void
    {
        Bus::fake();

        Country::factory()->create(['locales' => ['en']]);

        $job = new SyncRecipesJob();
        $job->handle();

        Bus::assertBatched(function ($batch): bool {
            return $batch->options['queue'] === QueueEnum::HelloFresh->value;
        });
    }

    #[Test]
    public function it_orders_countries_by_id(): void
    {
        Bus::fake();

        Country::factory()->create(['locales' => ['de']]);
        Country::factory()->create(['locales' => ['en']]);

        $job = new SyncRecipesJob();
        $job->handle();

        Bus::assertBatched(function ($batch): bool {
            $jobs = $batch->jobs;
            $firstJob = $jobs->first();
            $lastJob = $jobs->last();

            return $firstJob->country->id < $lastJob->country->id;
        });
    }

    #[Test]
    public function it_does_not_dispatch_jobs_when_no_countries_exist(): void
    {
        Bus::fake();

        $job = new SyncRecipesJob();
        $job->handle();

        Bus::assertBatched(function ($batch) {
            return $batch->jobs->isEmpty();
        });
    }
}
