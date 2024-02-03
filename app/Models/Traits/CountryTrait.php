<?php

namespace App\Models\Traits;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

trait CountryTrait
{
    /**
     * Get the table associated with the model.
     */
    public function getTable(): string
    {
        $this->table = App::getCountryPrefix() . Str::snake(Str::pluralStudly(class_basename($this)));

        return $this->table;
    }

    /**
     * Get the joining table name for a many-to-many relation.
     */
    public function joiningTable($related, $instance = null): string
    {
        return $this->getTable();
    }
}
