<?php

namespace App\Console\Commands\Development;

use App\Database\Migrations\CommandTrait;
use Illuminate\Database\Console\Migrations\RollbackCommand as Command;
use Illuminate\Database\Migrations\Migrator;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:development:migrate:rollback')]
class RollbackCommand extends Command
{
    use CommandTrait;

    protected string $newSignature = 'app:development:migrate:rollback {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--pretend : Dump the SQL queries that would be run}
                {--step : The number of migrations to be reverted}
                {--batch : The batch of migrations (identified by their batch number) to be reverted}';

    public function __construct(Migrator $migrator)
    {
        $this->updateSignature();
        parent::__construct($migrator);
    }
}
