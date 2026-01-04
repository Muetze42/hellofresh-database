<?php

namespace App\Jobs\Country\ActivateCountryResources;

use App\Enums\QueueEnum;
use App\Models\Country;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Context;

/**
 * Abstract base job for activating country resources based on recipe count.
 */
abstract class AbstractActivateResourceJob implements ShouldQueue
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
        $threshold = $this->getMinimumRecipeCount();

        $modelClass::where('country_id', $this->country->getKey())
            ->withCount(['recipes' => fn (Builder $query) => $query->whereNull('recipes.deleted_at')])
            ->each(fn (Model $model) => $model->updateQuietly([
                'active' => (int) $model->getAttribute('recipes_count') > $threshold,
            ]));
    }

    /**
     * Get the model class to process.
     *
     * @return class-string<Model>
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the minimum recipe count threshold for activation.
     */
    protected function getMinimumRecipeCount(): int
    {
        return 3;
    }
}
