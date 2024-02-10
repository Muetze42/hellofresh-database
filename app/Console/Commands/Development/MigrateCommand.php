<?php

namespace App\Console\Commands\Development;

use App\Database\Migrations\CommandTrait;
use App\Database\Migrations\Migrator;
use App\Models\Country;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Console\Migrations\MigrateCommand as Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:development:migrate')]
class MigrateCommand extends Command
{
    use CommandTrait;

    /**
     * The name and signature of the console command.
     */
    protected string $newSignature = 'app:development:migrate';

    /**
     * The console command description.
     */
    protected $description = 'Run the database migrations for countries';

    /**
     * The Migration Repository Table.
     */
    protected string $migrationTable;

    /**
     * The Country instance.
     */
    protected Country $country;

    /**
     * The database table prefix for a country.
     */
    protected string $prefix;

    public function __construct(Migrator $migrator, Dispatcher $dispatcher)
    {
        $this->updateSignature();
        parent::__construct($migrator, $dispatcher);
    }

    /**
     * Prepare the migration database for running.
     */
    protected function prepareDatabase(): void
    {
        if (!$this->repositoryExists()) {
            $this->components->info('Preparing database.');

            $this->components->task('Creating migration table', function () {
                return $this->callSilent(
                    'migrate:install',
                    array_filter([
                        '--database' => $this->option('database') . ':' . $this->prefix . $this->migrationTable,
                    ])
                ) == 0;
            });

            $this->newLine();
        }

        if (!$this->migrator->hasRunAnyMigrations() && !$this->option('pretend')) {
            $this->loadSchemaState();
        }
    }
}
