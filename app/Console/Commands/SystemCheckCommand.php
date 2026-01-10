<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Livewire;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'system:check-requirements')]
class SystemCheckCommand extends Command
{
    /**
     * The minimum upload size.
     */
    protected ?string $minUploadSize = '10M';

    /**
     * The minimum memory limit.
     */
    protected ?string $minMemoryLimit = '256M';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check-requirements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifies that all system prerequisites for the application have been satisfied.';

    /**
     * Indicates whether an error occurred during the command execution.
     */
    protected bool $hasError = false;

    /**
     * The required PHP extensions.
     *
     * @var list<string>
     */
    protected array $requiredPhpExtensions = [
        'curl',
        'gd',
        'imagick',
        'intl',
        'json',
        'mbstring',
        'openssl',
        'pdo',
        'pgsql',
        'redis',
        'tokenizer',
        'xml',
        'zip',
    ];

    /**
     * The required Binaries.
     *
     * @var list<string>
     */
    protected array $requiredBinaries = [
        // 'ffmpeg',
        // 'ghostscript',
        // 'libreoffice',
        // 'qpdf',
        // 'pnpm',
        'unzip',
        'zip',
    ];

    /**
     * Execute the console command.
     *
     * @throws RuntimeException
     */
    public function handle(): void
    {
        if ($this->shouldCheckMinUploadSize()) {
            $this->checkUploadSize();
        }

        if ($this->shouldCheckMemoryLimit()) {
            $this->checkMemoryLimit();
        }

        $this->checkPhpExtensions();
        $this->checkBinaries();

        if ($this->hasError) {
            throw new RuntimeException('System requirements not met. Please check the error messages above.');
        }

        $this->components->info('All system requirements satisfied.');
    }

    /**
     * Checks the availability of required binaries in the system's PATH.
     */
    protected function checkBinaries(): void
    {
        foreach ($this->requiredBinaries as $requiredBinary) {
            $result = Process::run(PHP_OS_FAMILY === 'Windows' ? 'where ' . $requiredBinary : 'command -v ' . $requiredBinary);

            if (! $result->successful()) {
                $this->error(
                    sprintf('The required binary `%s` is not installed or not available in PATH.', $requiredBinary)
                );
                $this->hasError = true;
            }
        }
    }

    /**
     * Verifies that the PHP `memory_limit` setting meets the minimum required value.
     */
    protected function checkMemoryLimit(): void
    {
        $memoryLimit = $this->getPhpIniOption('memory_limit');
        $minMemoryLimit = $this->minMemoryLimit();

        if ($memoryLimit < $minMemoryLimit && $memoryLimit !== -1) {
            $this->error('The `memory_limit` PHP setting is too low.');

            $this->hasError = true;
        }
    }

    /**
     * Checks if all required PHP extensions loaded.
     */
    protected function checkPhpExtensions(): void
    {
        foreach ($this->requiredPhpExtensions as $requiredPhpExtension) {
            if (! extension_loaded($requiredPhpExtension)) {
                $this->error(sprintf('The PHP extension `%s` is not installed.', $requiredPhpExtension));

                $this->hasError = true;
            }
        }
    }

    /**
     * Validates if the PHP `upload_max_filesize` configuration meets the minimum required upload size.
     */
    protected function checkUploadSize(): void
    {
        $minUploadSize = $this->minUploadSize();

        if ($this->getPhpIniOption('upload_max_filesize') < $minUploadSize) {
            $this->error('The `upload_max_filesize` PHP setting is too low.');

            $this->hasError = true;
        }

        if ($this->getPhpIniOption('post_max_size') < $minUploadSize) {
            $this->error('The `post_max_size` PHP setting is too low.');

            $this->hasError = true;
        }

        if (class_exists(Livewire::class) && class_exists(FileUploadConfiguration::class)) {
            $rules = FileUploadConfiguration::rules();

            $maxFileSize = $this->extractMaxFileSizes($rules);

            if ($maxFileSize < $minUploadSize) {
                $this->error('The Livewire `temporary_file_upload.rules` configuration is too low.');
                $this->hasError = true;
            }

            $maxFileSize = $this->extractMaxFileSizes($rules);

            if ($maxFileSize < $minUploadSize) {
                $this->error('The Livewire `temporary_file_upload.rules` configuration is too low.');
                $this->hasError = true;
            }
        }

        if (class_exists(Media::class)) {
            $maxFileSize = config('media-library.max_file_size');

            if ($maxFileSize < $minUploadSize) {
                $this->error('The Media Library `max_file_size` configuration is too low.');
                $this->hasError = true;
            }
        }
    }

    /**
     * Extracts the smallest maximum file size defined in the provided validation rules.
     *
     * @param  array<string, mixed>  $rules
     */
    protected function extractMaxFileSizes(?array $rules): ?int
    {
        if ($rules === null || $rules === []) {
            return null;
        }

        $maxSizes = [];

        foreach ($rules as $rule) {
            if (is_string($rule) && str_starts_with($rule, 'max:')) {
                $maxSizes[] = (int) substr($rule, 4);
            }
        }

        return $maxSizes !== [] ? min($maxSizes) * 1024 : null;
    }

    /**
     * Retrieves a PHP configuration option value. Optionally converts the value into bytes.
     */
    protected function getPhpIniOption(string $option): int
    {
        $value = ini_get($option);

        if ($value === false) {
            return 0;
        }

        return $this->iniSizeToBytes($value);
    }

    /**
     * Determines whether the minimum upload size should check.
     */
    protected function shouldCheckMinUploadSize(): bool
    {
        return $this->minUploadSize() > 0;
    }

    /**
     * Determines whether the memory limit should check.
     */
    protected function shouldCheckMemoryLimit(): bool
    {
        return $this->minMemoryLimit() > 0;
    }

    /**
     * Returns the minimum upload size in bytes, if defined.
     */
    protected function minUploadSize(): ?int
    {
        return $this->iniSizeToBytes($this->minUploadSize);
    }

    /**
     * Returns the minimum memory limit in bytes, if defined.
     */
    protected function minMemoryLimit(): ?int
    {
        return $this->iniSizeToBytes($this->minMemoryLimit);
    }

    /**
     * Converts a given size value from a human-readable format (e.g., "10 M", "1G") into bytes.
     */
    protected function iniSizeToBytes(?string $value): int
    {
        if (! $value) {
            return 0;
        }

        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $bytes = (int) $value;

        return match ($unit) {
            'k' => $bytes * 1024,
            'm' => $bytes * 1024 ** 2,
            'g' => $bytes * 1024 ** 3,
            't' => $bytes * 1024 ** 4,
            default => $bytes,
        };
    }
}
