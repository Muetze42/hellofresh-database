<?php

namespace App\Jobs\Country\UpdateRecipeCount;

use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Context;

/**
 * Abstract base job for updating recipe counts on country resources.
 */
abstract class AbstractUpdateRecipeCountJob implements ShouldQueue
{
    use Batchable;
    use Queueable;

    public function __construct(
        protected Country $country,
    ) {
        $this->onQueue(QueueEnum::Long->value);
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        Context::add('country', $this->country->id);

        $modelClass = $this->getModelClass();

        $modelClass::where('country_id', $this->country->getKey())
            ->withCount(['recipes' => fn (Builder $query): Builder => $query->whereNull('recipes.deleted_at')])
            ->each(static function (Model $model): void {
                /** @var int $recipesCount */
                $recipesCount = $model->getAttribute('recipes_count') ?? 0;
                $model->updateQuietly([
                    'cached_recipes_count' => $recipesCount,
                ]);
            });
    }

    /**
     * Get the model class to process.
     *
     * @return class-string<Model>
     */
    abstract protected function getModelClass(): string;
}
