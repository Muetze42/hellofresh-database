<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Enums\QueueEnum;
use App\Jobs\FetchMenusJob;
use App\Jobs\SyncMenusJob;
use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SyncMenusJobTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_be_instantiated(): void
    {
        $job = new SyncMenusJob();

        $this->assertInstanceOf(SyncMenusJob::class, $job);
    }

    #[Test]
    public function it_uses_hellofresh_queue(): void
    {
        $job = new SyncMenusJob();

        $this->assertSame(QueueEnum::HelloFresh->value, $job->queue);
    }

    #[Test]
    public function it_dispatches_fetch_jobs_for_each_country(): void
    {
        Bus::fake();

        Country::factory()->count(3)->create();

        $job = new SyncMenusJob();
        $job->handle();

        Bus::assertBatched(fn ($batch): bool => $batch->jobs->count() === 3);
    }

    #[Test]
    public function it_creates_fetch_jobs_with_correct_country(): void
    {
        Bus::fake();

        $country = Country::factory()->create();

        $job = new SyncMenusJob();
        $job->handle();

        Bus::assertBatched(function ($batch) use ($country): bool {
            $jobs = $batch->jobs;

            return $jobs->count() === 1
                && $jobs->first() instanceof FetchMenusJob
                && $jobs->first()->country->is($country);
        });
    }

    #[Test]
    public function it_names_the_batch_correctly(): void
    {
        Bus::fake();

        Country::factory()->create();

        $job = new SyncMenusJob();
        $job->handle();

        Bus::assertBatched(fn ($batch): bool => $batch->name === 'Sync HelloFresh Menus');
    }

    #[Test]
    public function it_uses_correct_queue_for_batch(): void
    {
        Bus::fake();

        Country::factory()->create();

        $job = new SyncMenusJob();
        $job->handle();

        Bus::assertBatched(fn ($batch): bool => $batch->options['queue'] === QueueEnum::HelloFresh->value);
    }

    #[Test]
    public function it_orders_countries_by_id(): void
    {
        Bus::fake();

        $country2 = Country::factory()->create();
        $country1 = Country::factory()->create();
        $country3 = Country::factory()->create();

        $job = new SyncMenusJob();
        $job->handle();

        Bus::assertBatched(function ($batch) use ($country1, $country2, $country3): bool {
            $jobs = $batch->jobs;

            return $jobs->count() === 3
                && $jobs[0]->country->is($country2)
                && $jobs[1]->country->is($country1)
                && $jobs[2]->country->is($country3);
        });
    }

    #[Test]
    public function it_does_not_dispatch_jobs_when_no_countries_exist(): void
    {
        Bus::fake();

        $job = new SyncMenusJob();
        $job->handle();

        Bus::assertBatched(fn ($batch): bool => $batch->jobs->isEmpty());
    }
}
