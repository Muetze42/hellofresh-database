<?php

namespace App\Jobs;

use App\Contracts\LauncherJobInterface;
use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

/**
 * @method static void dispatch()
 */
class GenerateSitemapsJob implements LauncherJobInterface, ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue(QueueEnum::Long->value);
    }

    /**
     * The console command description.
     */
    public static function description(): string
    {
        return 'Generate sitemaps for all active countries and locales';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->ensureSitemapDirectoryExists();

        $countries = Country::query()
            ->active()
            ->orderBy('code')
            ->get();

        foreach ($countries as $country) {
            foreach ($country->locales as $locale) {
                GenerateSitemapJob::dispatch($country, $locale);
            }
        }
    }

    /**
     * Ensure the sitemaps directory exists.
     */
    protected function ensureSitemapDirectoryExists(): void
    {
        if (! Storage::disk('public')->exists('sitemaps')) {
            Storage::disk('public')->makeDirectory('sitemaps');
        }
    }
}
