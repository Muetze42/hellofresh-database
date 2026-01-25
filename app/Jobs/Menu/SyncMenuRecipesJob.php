<?php

namespace App\Jobs\Menu;

use App\Enums\QueueEnum;
use App\Models\Menu;
use App\Models\Recipe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncMenuRecipesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  list<string>  $hellofreshIds
     */
    public function __construct(
        public int $menuId,
        public int $countryId,
        public array $hellofreshIds,
    ) {
        $this->onQueue(QueueEnum::Import->value);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $menu = Menu::find($this->menuId);

        if ($menu === null) {
            return;
        }

        $recipeIds = Recipe::where('country_id', $this->countryId)
            ->whereIn('hellofresh_id', $this->hellofreshIds)
            ->pluck('id')
            ->toArray();

        if ($recipeIds === []) {
            return;
        }

        $menu->recipes()->sync($recipeIds);
    }

    /**
     * Determine numbers of times the job may be attempted.
     */
    public function tries(): int
    {
        return 1;
    }
}
