<?php

namespace App\Models;

use App\Models\Traits\CountryTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'name',
        'type',
    ];
}
