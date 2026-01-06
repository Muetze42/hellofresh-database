<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use JsonException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

#[AsCommand(name: 'livewire:check-inheritance')]
class CheckLivewireComponentInheritanceCommand extends Command
{
    protected $signature = 'livewire:check-inheritance
                            {--json : Output results as JSON}';

    protected $description = 'Check that all Livewire components extend App\Livewire\AbstractComponent instead of Livewire\Component';

    /**
     * @var array<int, array{file: string, class: string}>
     */
    protected array $violations = [];

    /**
     * @throws JsonException
     * @throws Throwable
     */
    public function handle(): void
    {
        $livewirePath = app_path('Livewire');

        if (! File::isDirectory($livewirePath)) {
            $this->outputError('Livewire directory not found: ' . $livewirePath);
            $this->fail();
        }

        $files = File::allFiles($livewirePath);

        foreach ($files as $file) {
            $this->checkFile($file);
        }

        if ($this->violations === []) {
            $this->outputSuccess();

            return;
        }

        $this->outputViolations();
        $this->fail();
    }

    protected function checkFile(SplFileInfo $file): void
    {
        if ($file->getExtension() !== 'php') {
            return;
        }

        $content = $file->getContents();
        $relativePath = str_replace(app_path() . '/', '', $file->getPathname());

        if (! preg_match('/^namespace\s+([^;]+);/m', $content, $namespaceMatch)) {
            return;
        }

        $namespace = $namespaceMatch[1];

        if (! str_starts_with($namespace, 'App\\Livewire')) {
            return;
        }

        if (preg_match('/^abstract\s+class\s+/m', $content)) {
            return;
        }

        if (! preg_match('/^class\s+(\w+)\s+extends\s+(\w+)/m', $content, $classMatch)) {
            return;
        }

        $className = $classMatch[1];
        $extendsClass = $classMatch[2];

        if ($extendsClass === 'AbstractComponent') {
            return;
        }

        if ($extendsClass !== 'Component') {
            return;
        }

        if (! preg_match('/use\s+Livewire\\\\Component\s*;/', $content)) {
            return;
        }

        $this->violations[] = [
            'file' => $relativePath,
            'class' => $namespace . '\\' . $className,
        ];
    }

    /**
     * @throws JsonException
     */
    protected function outputSuccess(): void
    {
        if ($this->option('json')) {
            $this->line(json_encode([
                'success' => true,
                'message' => 'All Livewire components correctly extend AbstractComponent.',
                'violations' => [],
            ], JSON_THROW_ON_ERROR));

            return;
        }

        $this->info('All Livewire components correctly extend AbstractComponent.');
    }

    /**
     * @throws JsonException
     */
    protected function outputError(string $message): void
    {
        if ($this->option('json')) {
            $this->line(json_encode([
                'success' => false,
                'message' => $message,
                'violations' => [],
            ], JSON_THROW_ON_ERROR));

            return;
        }

        $this->error($message);
    }

    /**
     * @throws JsonException
     */
    protected function outputViolations(): void
    {
        if ($this->option('json')) {
            $this->line(json_encode([
                'success' => false,
                'message' => sprintf('%d component(s) extend Livewire\Component instead of AbstractComponent', count($this->violations)),
                'violations' => $this->violations,
            ], JSON_THROW_ON_ERROR));

            return;
        }

        $this->error(sprintf('Found %d component(s) extending Livewire\Component directly:', count($this->violations)));
        $this->newLine();

        $this->table(
            ['File', 'Class'],
            array_map(fn (array $violation): array => [
                $violation['file'],
                $violation['class'],
            ], $this->violations)
        );

        $this->newLine();
        $this->line('These components should extend App\Livewire\AbstractComponent instead of Livewire\Component.');
    }
}
