<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utensil extends Model
{
    //use \Illuminate\Database\Eloquent\Factories\HasFactory; // Todo

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'external_id',
        'type',
        'name',
    ];
}
