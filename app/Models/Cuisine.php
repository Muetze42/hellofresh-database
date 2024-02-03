<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'type',
        'name',
        'icon_link',
        'icon_path',
        'usage',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'usage' => 'int',
    ];
}
