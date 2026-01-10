<?php

// phpcs:ignoreFile

declare(strict_types=1);

use Rector\CodingStyle\Rector\String_\SimplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\EarlyReturn\Rector\Return_\ReturnBinaryOrToEarlyReturnRector;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameVariableToMatchNewTypeRector;
use Rector\Naming\Rector\Foreach_\RenameForeachValueVariableToMatchMethodCallReturnTypeRector;
use Rector\Php74\Rector\Closure\ClosureToArrowFunctionRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Transform\Rector\FuncCall\FuncCallToStaticCallRector;
use RectorLaravel\Rector\If_\ThrowIfRector;
use RectorLaravel\Rector\StaticCall\DispatchToHelperFunctionsRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/support',
        __DIR__ . '/tests',
    ])
    ->withComposerBased(phpunit: true, laravel: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true, // Todo: https://getrector.com/find-rule?rectorSet=core-coding-style
        typeDeclarations: true,
        privatization: true,
        naming: true,
        instanceOf: true,
        earlyReturn: true,
        // strictBooleans: true,
        carbon: true,
        // rectorPreset: true,
        phpunitCodeQuality: true
    )
    ->withSets([
        LevelSetList::UP_TO_PHP_84,
        PHPUnitSetList::PHPUNIT_110,
        LaravelLevelSetList::UP_TO_LARAVEL_120,

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
         * Makes working with Laravel Factories easier and more IDE friendly.
         */
        // LaravelSetList::LARAVEL_FACTORIES,

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

        /**
         * Improves Laravel testing by converting deprecated methods and adding better assertions.
         */
        LaravelSetList::LARAVEL_TESTING,
    ])
    ->withSkip([
        /**
         * Rename variable to match method return type.
         *
         * @see https://getrector.com/rule-detail/rename-variable-to-match-method-call-return-type-rector
         */
        RenameVariableToMatchMethodCallReturnTypeRector::class,

        /**
         * Use the event or dispatch helpers instead of the static dispatch method.
         *
         * @see https://getrector.com/rule-detail/dispatch-to-helper-functions-rector
         */
        DispatchToHelperFunctionsRector::class,

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
         * Change if throw to throw_if
         *
         * @see https://getrector.com/rule-detail/throw-if-rector
         */
        ThrowIfRector::class,

        /**
         * Rename param to match ClassType
         *
         * @see https://getrector.com/rule-detail/rename-param-to-match-type-rector
         */
        RenameParamToMatchTypeRector::class,

        /**
         * Renames value variable name in foreach loop to match method type
         *
         * @see https://getrector.com/rule-detail/rename-foreach-value-variable-to-match-method-call-return-type-rector
         */
        RenameForeachValueVariableToMatchMethodCallReturnTypeRector::class,

        /**
         * Use the static factory method instead of global factory function.
         *
         * @see https://getrector.com/rule-detail/factory-func-call-to-static-call-rector
         */
        FuncCallToStaticCallRector::class,

        /**
         * Change closure to arrow function.
         *
         * @see https://getrector.com/rule-detail/closure-to-arrow-function-rector
         */
        ClosureToArrowFunctionRector::class,

        /**
         * Change simple property init and assign to constructor promotion.
         *
         * @see https://getrector.com/rule-detail/class-property-assign-to-constructor-promotion-rector
         */
        ClassPropertyAssignToConstructorPromotionRector::class,

        /**
         * Adds type hints and generic return types to improve Laravel code type safety.
         */
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,

        SimplifyQuoteEscapeRector::class,

        /**
         * Files.
         */
        __DIR__ . '/bootstrap/providers.php',
        __DIR__ . '/bootstrap/cache/*',
        // __DIR__ . '/database/seeders/*',
    ])
    ->withImportNames(
        // importDocBlockNames: false,
        removeUnusedImports: true
    );
// ->withTypeCoverageLevel(0)
// ->withDeadCodeLevel(0)
// ->withPhpLevel(0)
// ->withCodeQualityLevel(0)
// ->withCodingStyleLevel(0)
