<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\DiscoversClassesTrait;
use App\Contracts\LauncherCommandInterface;
use App\Contracts\LauncherJobInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'app:launcher')]
class LauncherCommand extends Command implements PromptsForMissingInput
{
    use DiscoversClassesTrait;

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

        // Collect constructor arguments
        $arguments = $this->collectJobArguments($jobClass);

        if ($arguments === null) {
            // User cancelled during argument collection
            $this->launcherMainMenu();

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
            'dispatch' => $jobClass::dispatch(...$arguments),
            'dispatchSync' => $this->dispatchJobSync($jobClass, $arguments),
            default => $this->launcherMainMenu(),
        };
    }

    /**
     * Collect constructor arguments for a job using prompts.
     *
     * @param  class-string  $jobClass
     * @return array<mixed>|null Returns null if user cancelled
     */
    protected function collectJobArguments(string $jobClass): ?array
    {
        try {
            $reflection = new ReflectionClass($jobClass);
            $constructor = $reflection->getConstructor();

            if ($constructor === null) {
                return [];
            }

            $parameters = $constructor->getParameters();
            $arguments = [];

            foreach ($parameters as $parameter) {
                $value = $this->promptForParameter($parameter);

                if ($value === '__CANCEL__') {
                    return null;
                }

                $arguments[] = $value;
            }

            return $arguments;
        } catch (ReflectionException) {
            return [];
        }
    }

    /**
     * Prompt the user for a parameter value based on its type.
     */
    protected function promptForParameter(ReflectionParameter $parameter): mixed
    {
        $name = $parameter->getName();
        $type = $parameter->getType();
        $isOptional = $parameter->isOptional();
        $defaultValue = $isOptional ? $parameter->getDefaultValue() : null;

        // Handle typed parameters
        if ($type instanceof ReflectionNamedType) {
            $typeName = $type->getName();

            // Boolean type
            if ($typeName === 'bool') {
                return $this->promptForBoolean($name, $defaultValue, $isOptional);
            }

            // Integer type
            if ($typeName === 'int') {
                return $this->promptForInteger($name, $defaultValue, $isOptional);
            }

            // String type
            if ($typeName === 'string') {
                return $this->promptForString($name, $defaultValue, $isOptional);
            }

            // Eloquent Model type
            if (class_exists($typeName) && is_subclass_of($typeName, Model::class)) {
                return $this->promptForModel($typeName, $name, $type->allowsNull(), $isOptional);
            }
        }

        // For unhandled types, return default or null
        return $defaultValue;
    }

    /**
     * Prompt for a boolean parameter.
     */
    protected function promptForBoolean(string $name, mixed $defaultValue, bool $isOptional): bool
    {
        $label = $this->formatParameterLabel($name);
        $default = is_bool($defaultValue) && $defaultValue;

        return confirm(
            label: $label,
            default: $default,
            hint: $isOptional ? 'Optional, default: ' . ($default ? 'Yes' : 'No') : ''
        );
    }

    /**
     * Prompt for an integer parameter.
     */
    protected function promptForInteger(string $name, mixed $defaultValue, bool $isOptional): ?int
    {
        $label = $this->formatParameterLabel($name);
        $default = is_int($defaultValue) ? (string) $defaultValue : '';

        $value = text(
            label: $label,
            default: $default,
            hint: $isOptional ? 'Optional, press Enter for default' : 'Required'
        );

        if ($value === '' && $isOptional) {
            return $defaultValue;
        }

        return (int) $value;
    }

    /**
     * Prompt for a string parameter.
     */
    protected function promptForString(string $name, mixed $defaultValue, bool $isOptional): ?string
    {
        $label = $this->formatParameterLabel($name);
        $default = is_string($defaultValue) ? $defaultValue : '';

        $value = text(
            label: $label,
            default: $default,
            hint: $isOptional ? 'Optional, press Enter for default' : 'Required'
        );

        if ($value === '' && $isOptional) {
            return $defaultValue;
        }

        return $value;
    }

    /**
     * Prompt for an Eloquent Model parameter.
     *
     * @param  class-string<Model>  $modelClass
     */
    protected function promptForModel(string $modelClass, string $name, bool $allowsNull, bool $isOptional): ?Model
    {
        $label = $this->formatParameterLabel($name);
        $shortName = class_basename($modelClass);

        // Build options from database
        $options = $this->getModelOptions($modelClass);

        if ($options === []) {
            $this->components->warn(sprintf('No %s records found in database.', $shortName));

            return null;
        }

        // Add "None" option for nullable/optional parameters
        if ($allowsNull || $isOptional) {
            $options = ['__NONE__' => sprintf('None (skip %s)', $shortName)] + $options;
        }

        $selected = select(
            label: $label,
            options: $options,
            hint: 'Select a ' . $shortName
        );

        if ($selected === '__NONE__') {
            return null;
        }

        return $modelClass::find($selected);
    }

    /**
     * Get options for a model select.
     *
     * @param  class-string<Model>  $modelClass
     * @return array<int|string, string>
     */
    protected function getModelOptions(string $modelClass): array
    {
        $models = $modelClass::query()->orderBy('id')->limit(100)->get();
        $options = [];

        foreach ($models as $model) {
            $label = $this->getModelLabel($model);
            $options[$model->getKey()] = $label;
        }

        return $options;
    }

    /**
     * Get a human-readable label for a model.
     */
    protected function getModelLabel(Model $model): string
    {
        // Try common name attributes
        foreach (['name', 'title', 'code', 'email', 'slug'] as $attribute) {
            if (isset($model->{$attribute}) && $model->{$attribute} !== null) {
                return $model->{$attribute} . ' (#' . $model->getKey() . ')';
            }
        }

        return class_basename($model) . ' #' . $model->getKey();
    }

    /**
     * Format a parameter name into a human-readable label.
     */
    protected function formatParameterLabel(string $name): string
    {
        return ucfirst(str_replace('_', ' ', $name));
    }

    /**
     * Dispatch a job synchronously and measure duration.
     *
     * @param  class-string  $jobClass
     * @param  array<mixed>  $arguments
     */
    protected function dispatchJobSync(string $jobClass, array $arguments = []): void
    {
        $this->components->info('Running job: ' . $jobClass);

        if ($arguments !== []) {
            $this->displayJobArguments($arguments);
        }

        $startTime = microtime(true);

        $jobClass::dispatchSync(...$arguments);

        $duration = microtime(true) - $startTime;

        $this->components->twoColumnDetail('Duration', $this->formatDuration($duration));
    }

    /**
     * Display the arguments that will be passed to the job.
     *
     * @param  array<mixed>  $arguments
     */
    protected function displayJobArguments(array $arguments): void
    {
        $this->components->bulletList(
            array_map(
                static function (mixed $value): string {
                    if ($value instanceof Model) {
                        return class_basename($value) . ' #' . $value->getKey();
                    }

                    if (is_bool($value)) {
                        return $value ? 'Yes' : 'No';
                    }

                    if ($value === null) {
                        return 'null';
                    }

                    return (string) $value;
                },
                $arguments
            )
        );
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
}
