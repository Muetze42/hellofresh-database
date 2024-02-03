<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use \App\Models\Traits\CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'type',
        'slug',
        'color_handle',
        'preferences',
        'display_label',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'preferences' => 'array',
        'display_label' => 'bool',
    ];
}
