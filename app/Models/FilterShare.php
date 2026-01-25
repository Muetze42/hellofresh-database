<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Override;

/**
 * @property array<string, mixed> $filters
 */
class FilterShare extends Model
{
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'page',
        'filters',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    #[Override]
    protected static function booted(): void
    {
        static::creating(function (FilterShare $filterShare): void {
            $filterShare->created_at = now();
        });
    }

    /**
     * Get the country that owns this filter share.
     *
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
