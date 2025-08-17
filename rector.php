<?php

// phpcs:ignoreFile

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    ->withComposerBased(phpunit: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true, // Todo: https://getrector.com/find-rule?rectorSet=core-coding-style
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        strictBooleans: true,
        carbon: true,
        // rectorPreset: true,
        phpunitCodeQuality: true
    )
    ->withSets([
        /**
         * Converts uses of things like `$app['config']` to `$app->make('config')`.
         */
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,

        /**
         * Converts most string and array helpers into Str and Arr Facades' static calls.
         */
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,

        /**
         * Replaces magical call on `$this->app["something"]` to standalone variable with PHPDocs.
         */
        LaravelSetList::LARAVEL_CODE_QUALITY,

        /**
         * Improves the usage of Laravel Collections by using simpler, more efficient, or more readable methods.
         */
        LaravelSetList::LARAVEL_COLLECTION,

        /**
         * Changes the string or class const used for a service container make call.
         */
        LaravelSetList::LARAVEL_CONTAINER_STRING_TO_FULLY_QUALIFIED_NAME,

        /**
         * Transforms magic method calls on Eloquent Models into corresponding Query Builder method calls.
         */
        // LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,

        /**
         * Replaces Facade aliases with full Facade names.
         */
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,

        /**
         * Replaces `abort()`, `report()`, `throw` statements inside conditions with `abort_if()`, `report_if()`, `throw_if()` function calls.
         */
        LaravelSetList::LARAVEL_IF_HELPERS,

        /**
         * Replaces Laravel's Facades with Dependency Injection.
         */
        // LaravelSetList::LARAVEL_STATIC_TO_INJECTION,

        /**
         * Migrates Eloquent legacy model factories (with closures) into class based factories.
         */
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
    ])
    ->withSkip([
        /**
         * Rename variable to match method return type.
         *
         * @see https://getrector.com/rule-detail/rename-variable-to-match-method-call-return-type-rector
         */
        // RenameVariableToMatchMethodCallReturnTypeRector::class,

        /**
         * Rename variable to match new ClassType.
         *
         * @see https://getrector.com/rule-detail/rename-variable-to-match-new-type-rector
         */
        RenameVariableToMatchNewTypeRector::class,

        /**
         * Changes Single return of || to early returns.
         *
         * @see https://getrector.com/rule-detail/return-binary-or-to-early-return-rector
         */
        ReturnBinaryOrToEarlyReturnRector::class,

        /**
         * Files.
         */
        __DIR__ . '/bootstrap/providers.php',
        __DIR__ . '/bootstrap/cache/*',
    ])
    ->withImportNames(
        importDocBlockNames: false,
        removeUnusedImports: true
    );
// ->withTypeCoverageLevel(0)
// ->withDeadCodeLevel(0)
// ->withPhpLevel(0)
// ->withCodeQualityLevel(0)
// ->withCodingStyleLevel(0)
