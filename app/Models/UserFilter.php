<?php

namespace App\Models;

use App\Contracts\Models\CountryTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFilter extends Model
{
    use HasFactory;
    use CountryTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the user that owns the stored filter.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
