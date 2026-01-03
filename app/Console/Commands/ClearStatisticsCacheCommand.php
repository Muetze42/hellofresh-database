<?php

namespace App\Console\Commands;

use App\Contracts\LauncherCommandInterface;
use App\Services\Portal\StatisticsService;
use Illuminate\Console\Command;
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
    protected $description = 'Clear and warm the portal statistics cache';

    /**
     * Execute the console command.
     */
    public function handle(StatisticsService $statistics): void
    {
        $this->components->task('Warming statistics cache', static fn () => $statistics->warmCache());
    }
}
