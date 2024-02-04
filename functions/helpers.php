<?php

use App\Models\Country;
use Illuminate\Support\Facades\App;

if (!function_exists('country')) {
    /**
     * Get the available Country instance.
     */
    function country(): ?Country
    {
        return App::country();
    }
}
