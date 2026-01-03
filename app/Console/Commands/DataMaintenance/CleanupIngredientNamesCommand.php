<?php

namespace App\Console\Commands\DataMaintenance;

use App\Models\Ingredient;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'data-maintenance:cleanup-ingredient-names')]
class CleanupIngredientNamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-maintenance:cleanup-ingredient-names
                            {--dry-run : Show what would be cleaned without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove trailing asterisks from ingredient names';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->components->info('Running in dry-run mode. No changes will be made.');
        }

        $ingredients = Ingredient::whereRaw("name::text LIKE '%*%'")->get();

        if ($ingredients->isEmpty()) {
            $this->components->info('No ingredients with asterisks found.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Found %d ingredients with asterisks.', $ingredients->count()));

        $updated = 0;

        foreach ($ingredients as $ingredient) {
            $names = $ingredient->getTranslations('name');
            $changed = false;

            foreach ($names as $locale => $name) {
                $cleaned = rtrim((string) $name, '*');

                if ($cleaned !== $name) {
                    $names[$locale] = $cleaned;
                    $changed = true;

                    $this->components->twoColumnDetail(
                        sprintf('<fg=yellow>%s</>: %s', $locale, $name),
                        $cleaned
                    );
                }
            }

            if ($changed) {
                if (! $dryRun) {
                    $ingredient->setTranslations('name', $names)->save();
                }

                $updated++;
            }
        }

        $action = $dryRun ? 'Would update' : 'Updated';
        $this->components->info(sprintf('%s %d ingredients.', $action, $updated));

        return self::SUCCESS;
    }
}
