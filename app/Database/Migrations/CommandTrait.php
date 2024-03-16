<?php

namespace App\Database\Migrations;

use App\Models\Country;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

trait CommandTrait
{
    /**
     * Change the signature.
     */
    protected function updateSignature(): void
    {
        $oldSignature = explode(' ', $this->signature)[0];
        $this->signature = $this->newSignature . substr($this->signature, strlen($oldSignature));
    }

    /**
     * Execute the console command.
     *
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    public function handle(): void
    {
        $this->migrationTable = Config::get('database.migrations.table');

        Country::each(function (Country $country) {
            $country->switch($country->locales[0]);
            $this->country = $country;
            $this->prefix = Str::lower($country->code) . '__';

            $repository = new DatabaseMigrationRepository(app('db'), $this->prefix . $this->migrationTable);
            $this->migrator = new Migrator($repository, app('db'), app('files'), app('events'), $this->prefix);

            parent::handle();
        });
    }

    /**
     * Get the path to the migration directory.
     *
     * @noinspection PhpMultipleClassDeclarationsInspection
     */
    protected function getMigrationPath(): string
    {
        return parent::getMigrationPath() . DIRECTORY_SEPARATOR . 'countries';
    }
}
