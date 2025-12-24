<?php

declare(strict_types=1);

namespace App\Console\Commands\DataMaintenance;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data-maintenance:merge-duplicate-ingredients')]
class MergeDuplicateIngredientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:merge-duplicate-ingredients
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge duplicate ingredients by name_slug and country_id';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->components->warn('DRY RUN - No changes will be made');
        }

        $duplicates = $this->findDuplicates();

        if ($duplicates === []) {
            $this->components->info('No duplicate ingredients found.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Found %d duplicate groups to merge.', count($duplicates)));

        $totalMerged = 0;
        $totalDeleted = 0;
        $totalPivotsMoved = 0;
        $totalDuplicatePivotsRemoved = 0;

        foreach ($duplicates as $duplicate) {
            $result = $this->mergeDuplicateGroup($duplicate, $dryRun);
            $totalMerged++;
            $totalDeleted += $result['deleted'];
            $totalPivotsMoved += $result['pivots_moved'];
            $totalDuplicatePivotsRemoved += $result['duplicate_pivots_removed'];
        }

        $this->newLine();
        $this->components->info('Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Duplicate groups merged', $totalMerged],
                ['Ingredients deleted', $totalDeleted],
                ['Pivot entries moved', $totalPivotsMoved],
                ['Duplicate pivots removed', $totalDuplicatePivotsRemoved],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->components->warn('DRY RUN - No changes were made. Run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    /**
     * Find all duplicate ingredient groups.
     *
     * @return array<int, array{name_slug: string, country_id: int, ids: non-empty-list<int>, count: int}>
     */
    protected function findDuplicates(): array
    {
        return DB::table('ingredients')
            ->select([
                'name_slug',
                'country_id',
                DB::raw('ARRAY_AGG(id ORDER BY id) as ids'),
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy(['name_slug', 'country_id'])
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('count')
            ->get()
            ->map(function (object $row): array {
                // Parse PostgreSQL array format {1,2,3} to PHP array
                $ids = trim((string) $row->ids, '{}');

                return [
                    'name_slug' => (string) $row->name_slug,
                    'country_id' => (int) $row->country_id,
                    'ids' => array_map(intval(...), explode(',', $ids)),
                    'count' => (int) $row->count,
                ];
            })
            ->all();
    }

    /**
     * Merge a single duplicate group.
     *
     * @param  array{name_slug: string, country_id: int, ids: non-empty-list<int>, count: int}  $duplicate
     * @return array{deleted: int, pivots_moved: int, duplicate_pivots_removed: int}
     */
    protected function mergeDuplicateGroup(array $duplicate, bool $dryRun): array
    {
        $keepId = $duplicate['ids'][0]; // Keep the lowest ID
        $deleteIds = array_slice($duplicate['ids'], 1);

        $this->components->twoColumnDetail(
            sprintf('<fg=yellow>%s</> (country: %d)', $duplicate['name_slug'], $duplicate['country_id']),
            sprintf('Keep ID %d, delete ', $keepId) . implode(', ', $deleteIds)
        );

        $result = [
            'deleted' => count($deleteIds),
            'pivots_moved' => 0,
            'duplicate_pivots_removed' => 0,
        ];

        // Count what would be moved (used for dry-run, overwritten in actual merge)
        $result['pivots_moved'] = DB::table('ingredient_recipe')
            ->whereIn('ingredient_id', $deleteIds)
            ->count();

        if ($dryRun) {
            return $result;
        }

        DB::transaction(function () use ($keepId, $deleteIds, &$result): void {
            // 1. Merge hellofresh_ids from all duplicates into the keeper
            $this->mergeHelloFreshIds($keepId, $deleteIds);

            // 2. Move pivot entries, handling duplicates
            $result['pivots_moved'] = $this->movePivotEntries($keepId, $deleteIds);

            // 3. Remove duplicate pivot entries (same ingredient_id + recipe_id)
            $result['duplicate_pivots_removed'] = $this->removeDuplicatePivots($keepId);

            // 4. Delete the duplicate ingredients
            DB::table('ingredients')->whereIn('id', $deleteIds)->delete();
        });

        return $result;
    }

    /**
     * Merge hellofresh_ids from duplicates into the keeper.
     *
     * @param  list<int>  $deleteIds
     */
    protected function mergeHelloFreshIds(int $keepId, array $deleteIds): void
    {
        $allIds = array_merge([$keepId], $deleteIds);

        // Get all hellofresh_ids from all duplicates
        $allHelloFreshIds = DB::table('ingredients')
            ->whereIn('id', $allIds)
            ->pluck('hellofresh_ids')
            ->flatMap(function (?string $ids): array {
                if ($ids === null) {
                    return [];
                }

                $decoded = json_decode($ids, true);

                return is_array($decoded) ? $decoded : [];
            })
            ->unique()
            ->values()
            ->all();

        // Update the keeper with merged IDs
        DB::table('ingredients')
            ->where('id', $keepId)
            ->update(['hellofresh_ids' => json_encode($allHelloFreshIds)]);
    }

    /**
     * Move pivot entries from duplicates to the keeper.
     *
     * @param  list<int>  $deleteIds
     */
    protected function movePivotEntries(int $keepId, array $deleteIds): int
    {
        // Get existing recipe_ids for the keeper to avoid duplicates
        $existingRecipeIds = DB::table('ingredient_recipe')
            ->where('ingredient_id', $keepId)
            ->pluck('recipe_id')
            ->all();

        // Get unique recipe_ids from duplicates that don't exist in keeper
        $newRecipeIds = DB::table('ingredient_recipe')
            ->whereIn('ingredient_id', $deleteIds)
            ->whereNotIn('recipe_id', $existingRecipeIds)
            ->distinct()
            ->pluck('recipe_id')
            ->all();

        // Delete ALL pivot entries from duplicates first
        DB::table('ingredient_recipe')
            ->whereIn('ingredient_id', $deleteIds)
            ->delete();

        // Insert new unique entries for the keeper
        $insertData = array_map(fn (int $recipeId): array => [
            'ingredient_id' => $keepId,
            'recipe_id' => $recipeId,
        ], $newRecipeIds);

        if ($insertData !== []) {
            DB::table('ingredient_recipe')->insert($insertData);
        }

        return count($newRecipeIds);
    }

    /**
     * Remove duplicate pivot entries for the keeper.
     */
    protected function removeDuplicatePivots(int $keepId): int
    {
        // Find duplicate recipe_ids for this ingredient
        $duplicatePivots = DB::table('ingredient_recipe')
            ->select('recipe_id', DB::raw('COUNT(*) as count'), DB::raw('MIN(id) as keep_pivot_id'))
            ->where('ingredient_id', $keepId)
            ->groupBy('recipe_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $removed = 0;
        foreach ($duplicatePivots as $duplicatePivot) {
            $removed += DB::table('ingredient_recipe')
                ->where('ingredient_id', $keepId)
                ->where('recipe_id', $duplicatePivot->recipe_id)
                ->whereNot('id', $duplicatePivot->keep_pivot_id)
                ->delete();
        }

        return $removed;
    }
}
