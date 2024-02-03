<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    //use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'uuid',
        'name',
        'slug',
        'type',
        'description',
        'priority',
        'icon_link',
        'icon_path',
        'usage_by_country',
        'external_created_at',
        'external_updated_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'priority' => 'int',
        'usage_by_country' => 'array',
        'external_created_at' => 'datetime',
        'external_updated_at' => 'datetime',
    ];
}
