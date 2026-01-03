<?php

declare(strict_types=1);

namespace App\Console\Commands\DataMaintenance;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data-maintenance:cleanup-duplicate-allergens')]
class CleanupDuplicateAllergensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:cleanup-duplicate-allergens
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge duplicate allergens by name and country_id, preserving icon_path';

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
            $this->components->info('No duplicate allergens found.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Found %d duplicate groups to merge.', count($duplicates)));

        $totalMerged = 0;
        $totalDeleted = 0;
        $totalPivotsMoved = 0;

        foreach ($duplicates as $duplicate) {
            $result = $this->mergeDuplicateGroup($duplicate, $dryRun);
            $totalMerged++;
            $totalDeleted += $result['deleted'];
            $totalPivotsMoved += $result['pivots_moved'];
        }

        $this->newLine();
        $this->components->info('Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Duplicate groups merged', $totalMerged],
                ['Allergens deleted', $totalDeleted],
                ['Pivot entries moved', $totalPivotsMoved],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->components->warn('DRY RUN - No changes were made. Run without --dry-run to apply.');
        }

        return self::SUCCESS;
    }

    /**
     * Find all duplicate allergen groups.
     *
     * @return array<int, array{name: string, country_id: int, ids: non-empty-list<int>, count: int}>
     */
    protected function findDuplicates(): array
    {
        return DB::table('allergens')
            ->select([
                'name',
                'country_id',
                DB::raw('ARRAY_AGG(id ORDER BY id) as ids'),
                DB::raw('COUNT(*) as count'),
            ])
            ->groupBy(['name', 'country_id'])
            ->havingRaw('COUNT(*) > 1')
            ->orderByDesc('count')
            ->get()
            ->map(function (object $row): array {
                // Parse PostgreSQL array format {1,2,3} to PHP array
                $ids = trim((string) $row->ids, '{}');

                return [
                    'name' => (string) $row->name,
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
     * @param  array{name: string, country_id: int, ids: non-empty-list<int>, count: int}  $duplicate
     * @return array{deleted: int, pivots_moved: int}
     */
    protected function mergeDuplicateGroup(array $duplicate, bool $dryRun): array
    {
        $keepId = $this->selectBestAllergen($duplicate['ids']);
        $deleteIds = array_values(array_filter($duplicate['ids'], fn (int $id): bool => $id !== $keepId));

        // Get name for display
        $name = json_decode($duplicate['name'], true);
        $displayName = is_array($name) ? (string) array_values($name)[0] : $duplicate['name'];

        $this->components->twoColumnDetail(
            sprintf('<fg=yellow>%s</> (country: %d)', $displayName, $duplicate['country_id']),
            sprintf('Keep ID %d, delete %s', $keepId, implode(', ', $deleteIds))
        );

        $result = [
            'deleted' => count($deleteIds),
            'pivots_moved' => 0,
        ];

        // Count what would be moved
        $result['pivots_moved'] = DB::table('allergen_recipe')
            ->whereIn('allergen_id', $deleteIds)
            ->count();

        if ($dryRun) {
            return $result;
        }

        DB::transaction(function () use ($keepId, $deleteIds, &$result): void {
            // 1. Merge hellofresh_ids from all duplicates into the keeper
            $this->mergeHelloFreshIds($keepId, $deleteIds);

            // 2. Merge icon_path if keeper doesn't have one
            $this->mergeIconPath($keepId, $deleteIds);

            // 3. Move pivot entries
            $result['pivots_moved'] = $this->movePivotEntries($keepId, $deleteIds);

            // 4. Delete the duplicate allergens
            DB::table('allergens')->whereIn('id', $deleteIds)->delete();
        });

        return $result;
    }

    /**
     * Select the best allergen to keep (prefer one with icon_path).
     *
     * @param  non-empty-list<int>  $ids
     */
    protected function selectBestAllergen(array $ids): int
    {
        // Find the one with icon_path
        $withIcon = DB::table('allergens')
            ->whereIn('id', $ids)
            ->whereNotNull('icon_path')
            ->value('id');

        if ($withIcon !== null) {
            return (int) $withIcon;
        }

        // Fallback to lowest ID
        return min($ids);
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
        $allHelloFreshIds = DB::table('allergens')
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
        DB::table('allergens')
            ->where('id', $keepId)
            ->update(['hellofresh_ids' => json_encode($allHelloFreshIds)]);
    }

    /**
     * Merge icon_path from duplicates into the keeper if keeper doesn't have one.
     *
     * @param  list<int>  $deleteIds
     */
    protected function mergeIconPath(int $keepId, array $deleteIds): void
    {
        // Check if keeper already has icon_path
        $keeperIconPath = DB::table('allergens')
            ->where('id', $keepId)
            ->value('icon_path');

        if ($keeperIconPath !== null) {
            return;
        }

        // Find icon_path from duplicates
        $iconPath = DB::table('allergens')
            ->whereIn('id', $deleteIds)
            ->whereNotNull('icon_path')
            ->value('icon_path');

        if ($iconPath === null) {
            return;
        }

        DB::table('allergens')
            ->where('id', $keepId)
            ->update(['icon_path' => $iconPath]);
    }

    /**
     * Move pivot entries from duplicates to the keeper.
     *
     * @param  list<int>  $deleteIds
     */
    protected function movePivotEntries(int $keepId, array $deleteIds): int
    {
        // Get existing recipe_ids for the keeper to avoid duplicates
        $existingRecipeIds = DB::table('allergen_recipe')
            ->where('allergen_id', $keepId)
            ->pluck('recipe_id')
            ->all();

        // Get unique recipe_ids from duplicates that don't exist in keeper
        $newRecipeIds = DB::table('allergen_recipe')
            ->whereIn('allergen_id', $deleteIds)
            ->whereNotIn('recipe_id', $existingRecipeIds)
            ->distinct()
            ->pluck('recipe_id')
            ->all();

        // Delete ALL pivot entries from duplicates first
        DB::table('allergen_recipe')
            ->whereIn('allergen_id', $deleteIds)
            ->delete();

        // Insert new unique entries for the keeper
        $insertData = array_map(fn (int $recipeId): array => [
            'allergen_id' => $keepId,
            'recipe_id' => $recipeId,
        ], $newRecipeIds);

        if ($insertData !== []) {
            DB::table('allergen_recipe')->insert($insertData);
        }

        return count($newRecipeIds);
    }
}
