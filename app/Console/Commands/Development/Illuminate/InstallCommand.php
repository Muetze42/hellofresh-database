<?php

namespace App\Console\Commands\Development\Illuminate;

use Illuminate\Database\Console\Migrations\InstallCommand as Command;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

class InstallCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $database = $this->option('database');
        if ($database && str_contains($database, ':')) {
            $split = explode(':', $database);
            $this->input->setOption('database', $split[0]);

            $this->repository = new DatabaseMigrationRepository(app('db'), $split[1]);
        }

        parent::handle();
    }
}
