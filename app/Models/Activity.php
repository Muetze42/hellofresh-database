<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as Model;

class Activity extends Model
{
    /**
     * The name of the "updated at" column.
     */
    public const ?string UPDATED_AT = null;
}
