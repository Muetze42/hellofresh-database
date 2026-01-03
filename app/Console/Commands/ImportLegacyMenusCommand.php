<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Models\Menu;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:import:legacy-menus')]
class ImportLegacyMenusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import:legacy-menus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import menus from legacy system JSON files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $basePath = 'legacy-menus';

        if (! Storage::disk('local')->exists($basePath)) {
            $this->components->error('Legacy menus directory not found: storage/app/private/legacy-menus');

            return self::FAILURE;
        }

        $files = Storage::disk('local')->files($basePath);
        $menuFiles = collect($files)->filter(fn (string $file): bool => str_ends_with($file, '__menus.json'));

        if ($menuFiles->isEmpty()) {
            $this->components->error('No menu files found');

            return self::FAILURE;
        }

        $countries = Country::pluck('id', 'code')->mapWithKeys(
            fn (int $id, string $code): array => [strtolower($code) => $id]
        );

        $totalMenusImported = 0;
        $totalMenusSkipped = 0;
        $totalRelationsImported = 0;

        foreach ($menuFiles as $menuFile) {
            $countryCode = $this->extractCountryCode($menuFile);

            if (! $countries->has($countryCode)) {
                $this->components->warn(sprintf('Unknown country code: %s, skipping', $countryCode));

                continue;
            }

            /** @var int<0, max> $countryId */
            $countryId = $countries->get($countryCode);
            $pivotFile = str_replace('__menus.json', '__menu_recipe.json', $menuFile);

            $this->components->info(sprintf('Processing %s...', $countryCode));

            $result = $this->importCountryMenus($countryId, $menuFile, $pivotFile);

            $totalMenusImported += $result['menus_imported'];
            $totalMenusSkipped += $result['menus_skipped'];
            $totalRelationsImported += $result['relations_imported'];

            $this->components->twoColumnDetail(
                strtoupper($countryCode),
                sprintf(
                    '%d imported, %d skipped, %d relations',
                    $result['menus_imported'],
                    $result['menus_skipped'],
                    $result['relations_imported']
                )
            );
        }

        $this->newLine();
        $this->components->info(sprintf('Import complete: %d menus imported, %d skipped, %d relations', $totalMenusImported, $totalMenusSkipped, $totalRelationsImported));

        return self::SUCCESS;
    }

    /**
     * Extract country code from filename.
     */
    protected function extractCountryCode(string $filename): string
    {
        $basename = basename($filename);

        return explode('__', $basename)[0];
    }

    /**
     * Import menus for a specific country.
     *
     * @param  int<0, max>  $countryId
     * @return array{menus_imported: int, menus_skipped: int, relations_imported: int}
     */
    protected function importCountryMenus(int $countryId, string $menuFile, string $pivotFile): array
    {
        /** @var array<int, array{id: int, year_week: int, start: string}> $menusData */
        $menusData = json_decode((string) Storage::disk('local')->get($menuFile), true);

        /** @var array<int, array{menu_id: int, recipe_id: string}> $pivotData */
        $pivotData = Storage::disk('local')->exists($pivotFile)
            ? json_decode((string) Storage::disk('local')->get($pivotFile), true)
            : [];

        $existingMenus = Menu::where('country_id', $countryId)
            ->pluck('id', 'year_week');

        $recipeIdMap = $this->buildRecipeIdMap($pivotData);
        $pivotByLegacyMenuId = collect($pivotData)->groupBy('menu_id');

        $menusImported = 0;
        $menusSkipped = 0;
        $relationsImported = 0;

        foreach ($menusData as $menuData) {
            $yearWeek = (int) $menuData['year_week'];

            if ($existingMenus->has($yearWeek)) {
                $menusSkipped++;

                continue;
            }

            $menu = new Menu([
                'year_week' => $yearWeek,
                'start' => $this->parseDate($menuData['start']),
            ]);
            $menu->country_id = $countryId;
            $menu->save();

            $menusImported++;

            $legacyMenuId = $menuData['id'];
            $pivotEntries = $pivotByLegacyMenuId->get($legacyMenuId, collect());

            $recipeIds = $pivotEntries
                ->map(fn (array $entry): ?int => $recipeIdMap->get($entry['recipe_id']))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (count($recipeIds) > 0) {
                $menu->recipes()->attach($recipeIds);
                $relationsImported += count($recipeIds);
            }
        }

        return [
            'menus_imported' => $menusImported,
            'menus_skipped' => $menusSkipped,
            'relations_imported' => $relationsImported,
        ];
    }

    /**
     * Build a map of HelloFresh IDs to internal recipe IDs.
     *
     * @param  array<int, array{menu_id: int, recipe_id: string}>  $pivotData
     * @return Collection<string, int>
     */
    protected function buildRecipeIdMap(array $pivotData): Collection
    {
        $hellofreshIds = collect($pivotData)
            ->pluck('recipe_id')
            ->unique()
            ->values();

        if ($hellofreshIds->isEmpty()) {
            return collect();
        }

        return DB::table('recipes')
            ->whereIn('hellofresh_id', $hellofreshIds)
            ->pluck('id', 'hellofresh_id');
    }

    /**
     * Parse date from legacy format.
     *
     * @throws InvalidFormatException
     */
    protected function parseDate(string $date): Carbon
    {
        return Date::createFromFormat('j/n/Y', $date) ?? Date::parse($date);
    }
}
