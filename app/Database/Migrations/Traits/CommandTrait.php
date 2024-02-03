<?php

namespace App\Database\Migrations\Traits;

use App\Database\Migrations\Migrator;
use App\Models\Country;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

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
     */
    public function handle(): void
    {
        $this->migrationTable = Config::get('database.migrations');

        Country::each(function (Country $country) {
            $country->switch();
            $this->country = $country;
            $this->prefix = App::getCountryPrefix();

            $repository = new DatabaseMigrationRepository(app('db'), $this->prefix . $this->migrationTable);
            $this->migrator = new Migrator($repository, app('db'), app('files'), app('events'), $this->prefix);

            parent::handle();
        });
    }

    /**
     * Get the path to the migration directory.
     */
    protected function getMigrationPath(): string
    {
        return parent::getMigrationPath() . DIRECTORY_SEPARATOR . 'countries';
    }
}
