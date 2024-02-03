<?php

namespace App\Database\Migrations;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator as BaseMigrator;
use Illuminate\Filesystem\Filesystem;

class Migrator extends BaseMigrator
{
    /**
     * The table prefix.
     */
    protected string $prefix;

    /**
     * Create a new migrator instance.
     */
    public function __construct(
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files,
        Dispatcher $dispatcher = null,
        string $prefix = ''
    ) {
        $this->prefix = $prefix;

        parent::__construct($repository, $resolver, $files, $dispatcher);
    }

    /**
     * Run a migration method on the given connection.
     */
    protected function runMethod($connection, $migration, $method): void
    {
        $previousConnection = $this->resolver->getDefaultConnection();

        try {
            $this->resolver->setDefaultConnection($connection->getName());

            $migration->{$method}($this->prefix);
        } finally {
            $this->resolver->setDefaultConnection($previousConnection);
        }
    }
}
