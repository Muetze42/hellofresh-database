<?php

declare(strict_types=1);

namespace App\Console\Commands\DataMaintenance;

use App\Console\Commands\DataMaintenance\Contracts\DataMaintenanceCommandInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data-maintenance:cleanup-duplicate-tags')]
class CleanupDuplicateTagsCommand extends Command implements DataMaintenanceCommandInterface
{
    /**
     * Get the order in which this command should run.
     */
    public function getExecutionOrder(): int
    {
        return 40;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:cleanup-duplicate-tags
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge duplicate tags by name and country_id, preserving active and display_label flags';

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
            $this->components->info('No duplicate tags found.');

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
                ['Tags deleted', $totalDeleted],
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
     * Find all duplicate tag groups.
     *
     * @return array<int, array{name: string, country_id: int, ids: non-empty-list<int>, count: int}>
     */
    protected function findDuplicates(): array
    {
        return DB::table('tags')
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
        $keepId = $this->selectBestTag($duplicate['ids']);
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
        $result['pivots_moved'] = DB::table('recipe_tag')
            ->whereIn('tag_id', $deleteIds)
            ->count();

        if ($dryRun) {
            return $result;
        }

        DB::transaction(function () use ($keepId, $deleteIds, &$result): void {
            // 1. Merge hellofresh_ids from all duplicates into the keeper
            $this->mergeHelloFreshIds($keepId, $deleteIds);

            // 2. Merge active and display_label flags (preserve true values)
            $this->mergeFlags($keepId, $deleteIds);

            // 3. Move pivot entries
            $result['pivots_moved'] = $this->movePivotEntries($keepId, $deleteIds);

            // 4. Delete the duplicate tags
            DB::table('tags')->whereIn('id', $deleteIds)->delete();
        });

        return $result;
    }

    /**
     * Select the best tag to keep (prefer one with active=true and display_label=true).
     *
     * @param  non-empty-list<int>  $ids
     */
    protected function selectBestTag(array $ids): int
    {
        // Find one with both active and display_label true
        $withBothFlags = DB::table('tags')
            ->whereIn('id', $ids)
            ->where('active', true)
            ->where('display_label', true)
            ->value('id');

        if ($withBothFlags !== null) {
            return (int) $withBothFlags;
        }

        // Find one with active true
        $withActive = DB::table('tags')
            ->whereIn('id', $ids)
            ->where('active', true)
            ->value('id');

        if ($withActive !== null) {
            return (int) $withActive;
        }

        // Find one with display_label true
        $withDisplayLabel = DB::table('tags')
            ->whereIn('id', $ids)
            ->where('display_label', true)
            ->value('id');

        if ($withDisplayLabel !== null) {
            return (int) $withDisplayLabel;
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
        $allHelloFreshIds = DB::table('tags')
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
        DB::table('tags')
            ->where('id', $keepId)
            ->update(['hellofresh_ids' => json_encode($allHelloFreshIds)]);
    }

    /**
     * Merge active and display_label flags from duplicates into the keeper.
     * If any duplicate has true, the keeper should have true.
     *
     * @param  list<int>  $deleteIds
     */
    protected function mergeFlags(int $keepId, array $deleteIds): void
    {
        $allIds = array_merge([$keepId], $deleteIds);

        // Check if any tag has active=true
        $hasActive = DB::table('tags')
            ->whereIn('id', $allIds)
            ->where('active', true)
            ->exists();

        // Check if any tag has display_label=true
        $hasDisplayLabel = DB::table('tags')
            ->whereIn('id', $allIds)
            ->where('display_label', true)
            ->exists();

        // Update keeper with merged flags
        DB::table('tags')
            ->where('id', $keepId)
            ->update([
                'active' => $hasActive,
                'display_label' => $hasDisplayLabel,
            ]);
    }

    /**
     * Move pivot entries from duplicates to the keeper.
     *
     * @param  list<int>  $deleteIds
     */
    protected function movePivotEntries(int $keepId, array $deleteIds): int
    {
        // Get existing recipe_ids for the keeper to avoid duplicates
        $existingRecipeIds = DB::table('recipe_tag')
            ->where('tag_id', $keepId)
            ->pluck('recipe_id')
            ->all();

        // Get unique recipe_ids from duplicates that don't exist in keeper
        $newRecipeIds = DB::table('recipe_tag')
            ->whereIn('tag_id', $deleteIds)
            ->whereNotIn('recipe_id', $existingRecipeIds)
            ->distinct()
            ->pluck('recipe_id')
            ->all();

        // Delete ALL pivot entries from duplicates first
        DB::table('recipe_tag')
            ->whereIn('tag_id', $deleteIds)
            ->delete();

        // Insert new unique entries for the keeper
        $insertData = array_map(fn (int $recipeId): array => [
            'tag_id' => $keepId,
            'recipe_id' => $recipeId,
        ], $newRecipeIds);

        if ($insertData !== []) {
            DB::table('recipe_tag')->insert($insertData);
        }

        return count($newRecipeIds);
    }
}
