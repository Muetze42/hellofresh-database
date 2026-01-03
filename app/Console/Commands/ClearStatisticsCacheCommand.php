<?php

namespace App\Console\Commands;

use App\Contracts\LauncherCommandInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:stats:clear-cache')]
class ClearStatisticsCacheCommand extends Command implements LauncherCommandInterface
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:stats:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the cached portal statistics';

    /**
     * The cache keys used by the statistics.
     *
     * @var list<string>
     */
    protected array $cacheKeys = [
        'portal_global_stats',
        'portal_country_stats',
        'portal_newest_recipes',
        'portal_difficulty_distribution',
        'portal_recipe_quality',
        'portal_top_ingredients',
        'portal_top_tags',
        'portal_top_cuisines',
        'portal_recipes_per_month',
        'portal_user_engagement',
        'portal_avg_prep_times',
        'portal_data_health',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach ($this->cacheKeys as $cacheKey) {
            Cache::forget($cacheKey);
        }

        $this->components->info('Statistics cache cleared successfully.');

        return self::SUCCESS;
    }
}
