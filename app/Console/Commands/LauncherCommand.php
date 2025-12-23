<?php

namespace App\Console\Commands;

use App\Contracts\LauncherCommandInterface;
use App\Contracts\LauncherJobInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Foundation\Bus\Dispatchable;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\Finder;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;

#[AsCommand(name: 'app:launcher')]
class LauncherCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:launcher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactive launcher for commands and jobs';

    /**
     * The application namespace.
     */
    protected string $applicationNamespace;

    /**
     * @var array<string, string>
     */
    protected array $launcherOptions = [];

    /**
     * @var class-string[]
     */
    protected array $jobs = [];

    /**
     * @var class-string[]
     */
    protected array $commands = [];

    public function __construct()
    {
        $this->applicationNamespace = app()->getNamespace();
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->jobs = $this->getConsoleDispatchableJobs();
        $this->commands = $this->getLauncherCommands();

        $this->prepareSearchOptions();
        $this->launcherMainMenu();
    }

    /**
     * Display the main menu with search options.
     */
    protected function launcherMainMenu(): void
    {
        $selected = search(
            label: 'Search for a command or job',
            options: fn (string $value): array => $value !== ''
                ? $this->filterOptions($value)
                : $this->launcherOptions,
            placeholder: 'Start typing to search...'
        );

        if (is_string($selected) && $selected !== '') {
            $this->executeSelection($selected);
        }
    }

    /**
     * Prepare search options from jobs and commands.
     */
    protected function prepareSearchOptions(): void
    {
        foreach ($this->jobs as $job) {
            $this->launcherOptions[$job] = $job::description() . ' [Job]';
        }

        foreach ($this->commands as $command) {
            /** @var Command $instance */
            $instance = resolve($command);
            $this->launcherOptions[$command] = ($instance->getDescription() ?: $command) . ' [Command]';
        }
    }

    /**
     * Filter options based on search value.
     *
     * @return array<string, string>
     */
    protected function filterOptions(string $value): array
    {
        return array_filter(
            $this->launcherOptions,
            static fn (string $label): bool => str_contains(strtolower($label), strtolower($value))
        );
    }

    /**
     * Execute the selected command or job.
     */
    protected function executeSelection(string $selected): void
    {
        if (in_array($selected, $this->jobs, true)) {
            $this->dispatchJob($selected);

            return;
        }

        if (in_array($selected, $this->commands, true)) {
            if (
                confirm(
                    label: 'Do you want to run this command?',
                    hint: trim(str_replace('[Command]', '', $this->launcherOptions[$selected]))
                )
            ) {
                $this->call($selected);

                return;
            }

            $this->launcherMainMenu();
        }
    }

    /**
     * Dispatch a job.
     *
     * @param  class-string  $jobClass
     */
    protected function dispatchJob(string $jobClass): void
    {
        if (! method_exists($jobClass, 'dispatch')) {
            $this->error('Job class does not have dispatch method: ' . $jobClass);

            return;
        }

        $action = select(
            label: 'How do you want to execute this job?',
            options: [
                'back' => '«Back',
                'dispatch' => 'Dispatch (async)',
                'dispatchSync' => 'Dispatch Sync (synchronous)',

            ],
            hint: trim(str_replace('[Job]', '', $this->launcherOptions[$jobClass]))
        );

        /* @var Dispatchable $jobClass */
        match ($action) {
            'dispatch' => $jobClass::dispatch(),
            'dispatchSync' => $this->dispatchJobSync($jobClass),
            default => $this->launcherMainMenu(),
        };
    }

    /**
     * Dispatch a job synchronously and measure duration.
     *
     * @param  class-string  $jobClass
     */
    protected function dispatchJobSync(string $jobClass): void
    {
        $this->components->info('Running job: ' . $jobClass);

        $startTime = microtime(true);

        $jobClass::dispatchSync();

        $duration = microtime(true) - $startTime;

        $this->components->twoColumnDetail('Duration', $this->formatDuration($duration));
    }

    /**
     * Format duration in human-readable format.
     */
    protected function formatDuration(float $seconds): string
    {
        if ($seconds < 1) {
            return round($seconds * 1000) . 'ms';
        }

        if ($seconds < 60) {
            return round($seconds, 2) . 's';
        }

        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds - ($minutes * 60);

        if ($minutes < 60) {
            return $minutes . 'm ' . round($remainingSeconds) . 's';
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes - ($hours * 60);

        return $hours . 'h ' . $remainingMinutes . 'm ' . round($remainingSeconds) . 's';
    }

    /**
     * Get all job classes that implement LauncherJobInterface.
     *
     * @return class-string[]
     */
    protected function getConsoleDispatchableJobs(): array
    {
        return $this->getClassesByInterface(
            app_path('Jobs'),
            $this->applicationNamespace . 'Jobs',
            LauncherJobInterface::class,
            ['Contracts'],
            Dispatchable::class
        );
    }

    /**
     * Get all command classes that implement LauncherCommandInterface.
     *
     * @return class-string[]
     */
    protected function getLauncherCommands(): array
    {
        return $this->getClassesByInterface(
            app_path('Console/Commands'),
            $this->applicationNamespace . 'Console\Commands',
            LauncherCommandInterface::class,
            ['Contracts'],
            null,
            Command::class
        );
    }

    /**
     * Get all classes in a directory that implement a specific interface.
     *
     * @template T of object
     *
     * @param  string  $path  The directory path to search
     * @param  string  $namespace  The base namespace for the classes
     * @param  class-string<T>  $interface  The interface to filter by
     * @param  array<string>  $excludeDirs  Directories to exclude from search
     * @param  class-string|null  $requiredTrait  Optional trait that classes must use
     * @param  class-string|null  $requiredParentClass  Optional parent class that classes must extend
     * @return class-string<T>[]
     */
    protected function getClassesByInterface(
        string $path,
        string $namespace,
        string $interface,
        array $excludeDirs = ['Contracts'],
        ?string $requiredTrait = null,
        ?string $requiredParentClass = null
    ): array {
        if (! is_dir($path)) {
            return [];
        }

        $finder = Finder::create()
            ->files()
            ->name('*.php')
            ->in($path);

        foreach ($excludeDirs as $excludeDir) {
            $finder->exclude($excludeDir);
        }

        $classes = [];

        foreach ($finder as $file) {
            $className = $this->getClassNameFromPath(
                $file->getRelativePathname(),
                $namespace
            );
            if ($className === null) {
                continue;
            }

            if (! class_exists($className)) {
                continue;
            }

            if (! $this->hasInterface($className, $interface)) {
                continue;
            }

            if ($requiredTrait !== null && ! $this->hasTrait($className, $requiredTrait)) {
                continue;
            }

            if ($requiredParentClass !== null && ! $this->hasParentClass($className, $requiredParentClass)) {
                continue;
            }

            /** @var class-string<T> $className */
            $classes[] = $className;
        }

        return $classes;
    }

    /**
     * Get the fully qualified class name from relative path and namespace.
     *
     * @return class-string|null
     */
    protected function getClassNameFromPath(string $relativePath, string $baseNamespace): ?string
    {
        $className = $baseNamespace . '\\' . str_replace(
            ['/', '.php'],
            ['\\', ''],
            $relativePath
        );

        return class_exists($className) ? $className : null;
    }

    /**
     * Check if class implements a specific interface.
     *
     * @param  class-string  $className
     * @param  class-string  $interface
     */
    protected function hasInterface(string $className, string $interface): bool
    {
        try {
            $reflection = new ReflectionClass($className);

            return $reflection->implementsInterface($interface)
                && $reflection->isInstantiable();
        } catch (ReflectionException) {
            return false;
        }
    }

    /**
     * Check if class uses a specific trait (recursively).
     *
     * @param  class-string  $className
     * @param  class-string  $trait
     */
    protected function hasTrait(string $className, string $trait): bool
    {
        try {
            $traits = class_uses_recursive($className);

            return in_array($trait, $traits, true);
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Check if class extends a specific parent class.
     *
     * @param  class-string  $className
     * @param  class-string  $parentClass
     */
    protected function hasParentClass(string $className, string $parentClass): bool
    {
        try {
            return is_subclass_of($className, $parentClass);
        } catch (Throwable) {
            return false;
        }
    }
}
