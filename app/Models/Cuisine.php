<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'type',
        'name',
        'slug',
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
