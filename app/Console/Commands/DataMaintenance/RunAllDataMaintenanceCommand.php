<?php

declare(strict_types=1);

namespace App\Console\Commands\DataMaintenance;

use App\Console\Commands\DataMaintenance\Contracts\DataMaintenanceCommandInterface;
use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

#[AsCommand(name: 'data-maintenance:run-all')]
class RunAllDataMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:run-all
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all data maintenance commands in order';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->components->warn('DRY RUN - No changes will be made');
        }

        $commands = $this->discoverMaintenanceCommands();

        if ($commands === []) {
            $this->components->info('No data maintenance commands found.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Found %d data maintenance commands to run.', count($commands)));
        $this->newLine();

        $failed = 0;

        foreach ($commands as $command) {
            $commandName = $command->getName();

            if ($commandName === null) {
                continue;
            }

            $this->components->task($commandName, function () use ($commandName, $dryRun, &$failed): void {
                $options = $dryRun ? ['--dry-run' => true] : [];
                $exitCode = $this->callSilently($commandName, $options);

                if ($exitCode !== self::SUCCESS) {
                    $failed++;
                }
            });
        }

        $this->newLine();

        if ($failed > 0) {
            $this->components->error(sprintf('%d command(s) failed.', $failed));

            return self::FAILURE;
        }

        $this->components->info('All data maintenance commands completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Discover all commands implementing DataMaintenanceCommandInterface.
     *
     * @return list<DataMaintenanceCommandInterface&SymfonyCommand>
     */
    protected function discoverMaintenanceCommands(): array
    {
        /** @var Artisan $artisan */
        $artisan = $this->getApplication();

        $commands = [];

        foreach ($artisan->all() as $command) {
            if ($command instanceof DataMaintenanceCommandInterface) {
                $commands[] = $command;
            }
        }

        // Sort by execution order
        usort($commands, fn (DataMaintenanceCommandInterface $commandA, DataMaintenanceCommandInterface $commandB): int => $commandA->getExecutionOrder() <=> $commandB->getExecutionOrder());

        return $commands;
    }
}
