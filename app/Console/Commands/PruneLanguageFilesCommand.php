<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'lang:prune-json')]
class PruneLanguageFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:prune-json
                            {--dry-run : Show what would be removed without actually removing}
                            {--show-removed : Show each removed key in the output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unused translation keys from JSON language files';

    /**
     * Directories to scan for translation key usage.
     *
     * @var list<string>
     */
    protected array $scanDirectories = [
        'app',
        'resources/views',
        'routes',
        'vendor/laravel/framework/src',
    ];

    /**
     * File extensions to scan.
     *
     * @var list<string>
     */
    protected array $scanExtensions = [
        'php',
        'blade.php',
    ];

    /**
     * Execute the console command.
     *
     * @throws FileNotFoundException
     */
    public function handle(): int
    {
        $this->components->info('Scanning codebase for translation key usage...');

        $usedKeys = $this->findUsedTranslationKeys();
        $this->components->info(sprintf('Found %d unique translation keys in use.', $usedKeys->count()));

        $languageFiles = $this->getJsonLanguageFiles();

        if ($languageFiles->isEmpty()) {
            $this->components->warn('No JSON language files found.');

            return self::SUCCESS;
        }

        $this->components->info(sprintf('Processing %d JSON language files...', $languageFiles->count()));

        $totalRemoved = 0;
        $isDryRun = $this->option('dry-run');

        foreach ($languageFiles as $languageFile) {
            $removed = $this->pruneLanguageFile($languageFile, $usedKeys, $isDryRun);
            $totalRemoved += $removed;
        }

        if ($isDryRun) {
            $this->components->warn(sprintf('Dry run: %d keys would be removed.', $totalRemoved));

            return self::SUCCESS;
        }

        if ($totalRemoved > 0) {
            $this->components->info(sprintf('Successfully removed %d unused translation keys.', $totalRemoved));

            return self::SUCCESS;
        }

        $this->components->info('No unused translation keys found.');

        return self::SUCCESS;
    }

    /**
     * Find all translation keys used in the codebase.
     *
     * @return Collection<int, string>
     *
     * @throws FileNotFoundException
     */
    protected function findUsedTranslationKeys(): Collection
    {
        $usedKeys = collect();

        foreach ($this->scanDirectories as $scanDirectory) {
            $fullPath = base_path($scanDirectory);

            if (! File::isDirectory($fullPath)) {
                continue;
            }

            $files = $this->getFilesRecursively($fullPath);

            foreach ($files as $file) {
                $content = File::get($file->getPathname());
                $keys = $this->extractTranslationKeys($content);
                $usedKeys = $usedKeys->merge($keys);
            }
        }

        return $usedKeys->unique()->values();
    }

    /**
     * Get all files recursively from a directory.
     *
     * @return Collection<int, SplFileInfo>
     */
    protected function getFilesRecursively(string $directory): Collection
    {
        $files = collect();
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo) {
                continue;
            }

            if (! $file->isFile()) {
                continue;
            }

            foreach ($this->scanExtensions as $scanExtension) {
                if (str_ends_with($file->getFilename(), '.' . $scanExtension)) {
                    $files->push($file);

                    break;
                }
            }
        }

        return $files;
    }

    /**
     * Extract translation keys from file content.
     *
     * @return Collection<int, string>
     */
    protected function extractTranslationKeys(string $content): Collection
    {
        $keys = collect();

        $patterns = [
            // __('key') or __("key")
            '/__\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,|\))/',
            // trans('key') or trans("key")
            '/\btrans\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,|\))/',
            // trans_choice('key') or trans_choice("key")
            '/trans_choice\(\s*[\'"]([^\'"]+)[\'"]\s*,/',
            // @lang('key') or @lang("key")
            '/@lang\(\s*[\'"]([^\'"]+)[\'"]\s*\)/',
            // Lang::get('key') or Lang::get("key")
            '/Lang::get\(\s*[\'"]([^\'"]+)[\'"]\s*(?:,|\))/',
            // Lang::choice('key') or Lang::choice("key")
            '/Lang::choice\(\s*[\'"]([^\'"]+)[\'"]\s*,/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $keys = $keys->merge($matches[1]);
            }
        }

        return $keys;
    }

    /**
     * Get all JSON language files.
     *
     * @return Collection<int, string>
     */
    protected function getJsonLanguageFiles(): Collection
    {
        $langPath = lang_path();

        if (! File::isDirectory($langPath)) {
            return collect();
        }

        return collect(File::files($langPath))
            ->filter(fn (SplFileInfo $file): bool => $file->getExtension() === 'json')
            ->map(fn (SplFileInfo $file): string => $file->getPathname());
    }

    /**
     * Prune unused keys from a language file.
     *
     * @param  Collection<int, string>  $usedKeys
     *
     * @throws FileNotFoundException
     */
    protected function pruneLanguageFile(string $filePath, Collection $usedKeys, bool $isDryRun): int
    {
        $filename = basename($filePath);
        $content = File::get($filePath);

        /** @var array<string, string>|null $translations */
        $translations = json_decode($content, true);

        if (! is_array($translations)) {
            $this->components->error(sprintf('Failed to parse JSON file: %s', $filename));

            return 0;
        }

        $originalCount = count($translations);
        $prunedTranslations = [];
        $removedKeys = [];

        foreach ($translations as $key => $value) {
            if ($usedKeys->doesntContain($key)) {
                $removedKeys[] = $key;

                continue;
            }

            $prunedTranslations[$key] = $value;
        }

        $removedCount = count($removedKeys);

        if ($removedCount === 0) {
            $this->components->twoColumnDetail($filename, '<fg=green>No unused keys</>');

            return 0;
        }

        $this->components->twoColumnDetail(
            $filename,
            sprintf('<fg=yellow>%d keys to remove</> (keeping %d of %d)', $removedCount, count($prunedTranslations), $originalCount)
        );

        if ($this->option('show-removed')) {
            foreach ($removedKeys as $removedKey) {
                $this->line(sprintf('  <fg=red>- %s</>', mb_strimwidth($removedKey, 0, 70, '...')));
            }
        }

        if (! $isDryRun) {
            $jsonContent = json_encode($prunedTranslations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($jsonContent === false) {
                $this->components->error(sprintf('Failed to encode JSON for: %s', $filename));

                return 0;
            }

            File::put($filePath, $jsonContent . "\n");
        }

        return $removedCount;
    }
}
