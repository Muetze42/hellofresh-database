<?php

namespace App\Models;

use App\Contracts\Models\CountryTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipeUser extends Pivot
{
    use CountryTrait;
}
