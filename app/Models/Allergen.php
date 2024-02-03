<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'name',
        'slug',
        'description',
        'icon_path',
        'triggers_traces_of',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'triggers_traces_of' => 'bool',
    ];
}
