<?php

namespace App\Models;

use App\Models\Traits\CanActivateTrait;
use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CountryTrait;
    use CanActivateTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'average_rating',
        'canonical',
        'canonical_link',
        'card_link',
        'cloned_from',
        'comment',
        'country',
        'external_created_at',
        'description',
        'description_markdown',
        'difficulty',
        'favorites_count',
        'headline',
        'image_link',
        'image_path',
        'is_addon',
        'name',
        'ratings_count',
        'serving_size',
        'total_time',
        'uuid',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'average_rating' => 'int',
        'difficulty' => 'int',
        'favorites_count' => 'int',
        'ratings_count' => 'int',
        'serving_size' => 'int',
        'is_addon' => 'bool',
        'external_created_at' => 'timestamp',
    ];
}
