<?php

namespace App\Console\Commands\Concerns;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\Finder;
use Throwable;

trait DiscoversClassesTrait
{
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

            if (! $this->classHasInterface($className, $interface)) {
                continue;
            }

            if ($requiredTrait !== null && ! $this->classHasTrait($className, $requiredTrait)) {
                continue;
            }

            if ($requiredParentClass !== null && ! $this->classHasParentClass($className, $requiredParentClass)) {
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
    protected function classHasInterface(string $className, string $interface): bool
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
    protected function classHasTrait(string $className, string $trait): bool
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
    protected function classHasParentClass(string $className, string $parentClass): bool
    {
        try {
            return is_subclass_of($className, $parentClass);
        } catch (Throwable) {
            return false;
        }
    }
}
