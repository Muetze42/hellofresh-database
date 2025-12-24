<?php

use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * Sentry Laravel SDK configuration file.
 *
 * @see https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Data Source Name (DSN)
    |--------------------------------------------------------------------------
    |
    | Sentry automatically assigns you a Data Source Name (DSN) when you
    | create a project to start monitoring events in your app.
    | @see https://docs.sentry.io/product/sentry-basics/dsn-explainer/
    |
    */
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),

    /*
    |--------------------------------------------------------------------------
    | The release version of your application
    |--------------------------------------------------------------------------
    |
    | Example with dynamic git hash:
    | trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD'))
    |
    */
    'release' => env('SENTRY_RELEASE'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | When left empty or `null` the Laravel environment will be used
    | (usually discovered from `APP_ENV` in your `.env`)
    |
    */
    'environment' => env('SENTRY_ENVIRONMENT'),

    /*
    |--------------------------------------------------------------------------
    | Sample Rate
    |--------------------------------------------------------------------------
    |
    | Configures the sample rate for error events, in the range of 0.0 to 1.0.
    | The default is 1.0 which means that 100% of error events are sent.
    | If set to 0.1 only 10% of error events will be sent. Events are picked randomly.
    |
    | @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#sample-rate
    |
    */
    'sample_rate' => env('SENTRY_SAMPLE_RATE') === null ? 1.0 : (float) env('SENTRY_SAMPLE_RATE'),

    /*
    |--------------------------------------------------------------------------
    | Traces Sample Rate
    |--------------------------------------------------------------------------
    |
    | A number between 0 and 1, controlling the percentage chance a given transaction will be sent to Sentry.
    | (0 represents 0% while 1 represents 100%.) Applies equally to all transactions created in the app.
    | Either this or traces_sampler must be defined to enable tracing.
    |
    | @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#traces-sample-rate
    |
    */
    'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE') === null ? 0 :
        (float) env('SENTRY_TRACES_SAMPLE_RATE'),

    /*
    |--------------------------------------------------------------------------
    | Profiles Sample Rate
    |--------------------------------------------------------------------------
    |
    | @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#profiles-sample-rate
    |
    */
    'profiles_sample_rate' => env('SENTRY_PROFILES_SAMPLE_RATE') === null ?
        null : (float) env('SENTRY_PROFILES_SAMPLE_RATE'),

    /*
    |--------------------------------------------------------------------------
    | Send Default Pii
    |--------------------------------------------------------------------------
    |
    | If this flag is enabled, certain personally identifiable information (PII)
    |  is added by active integrations. By default, no such data is sent.
    |
    | @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#send-default-pii
    |
    */
    'send_default_pii' => env('SENTRY_SEND_DEFAULT_PII', false),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#ignore-exceptions
    'ignore_exceptions' => [
        CommandNotFoundException::class,
    ],

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#ignore-transactions
    'ignore_transactions' => [
        // Ignore Laravel's default health URL
        '/up', // Todo: Customize health URL if changed or not exists
    ],

    /*
    |--------------------------------------------------------------------------
    | Capture Laravel logs as breadcrumbs
    |--------------------------------------------------------------------------
    |
    | Sentry uses breadcrumbs to create a trail of events that happened prior to an issue.
    | These events are very similar to traditional logs, but can record more rich structured data.
    |
    | @see https://docs.sentry.io/platforms/php/guides/laravel/enriching-events/breadcrumbs/
    |
    */
    'breadcrumbs' => [
        /* Capture Laravel logs as breadcrumbs */
        'logs' => env(
            'SENTRY_BREADCRUMBS_LOGS_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture Laravel cache events (hits, writes etc.) as breadcrumbs */
        'cache' => env(
            'SENTRY_BREADCRUMBS_CACHE_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture Livewire components like routes as breadcrumbs */
        'livewire' => env(
            'SENTRY_BREADCRUMBS_LIVEWIRE_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture SQL queries as breadcrumbs */
        'sql_queries' => env(
            'SENTRY_BREADCRUMBS_SQL_QUERIES_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture SQL query bindings (parameters) in SQL query breadcrumbs */
        'sql_bindings' => env(
            'SENTRY_BREADCRUMBS_SQL_BINDINGS_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture queue job information as breadcrumbs */
        'queue_info' => env(
            'SENTRY_BREADCRUMBS_QUEUE_INFO_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture command information as breadcrumbs */
        'command_info' => env(
            'SENTRY_BREADCRUMBS_COMMAND_JOBS_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),

        /* Capture HTTP client request information as breadcrumbs */
        'http_client_requests' => env(
            'SENTRY_BREADCRUMBS_HTTP_CLIENT_REQUESTS_ENABLED',
            env('SENTRY_BREADCRUMBS_ENABLED', true)
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    |
    | With performance monitoring, Sentry tracks application performance, measuring metrics like throughput and latency,
    | and displaying the impact of errors across multiple services. Sentry captures distributed traces consisting of
    | transactions and spans to measure individual services and operations within those services.
    |
    | @see https://docs.sentry.io/product/performance/
    |
    */
    'tracing' => [
        /* Trace queue jobs as their own transactions (this enables tracing for queue jobs) */
        'queue_job_transactions' => env(
            'SENTRY_TRACE_QUEUE_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture queue jobs as spans when executed on the sync driver */
        'queue_jobs' => env(
            'SENTRY_TRACE_QUEUE_JOBS_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture SQL queries as spans */
        'sql_queries' => env(
            'SENTRY_TRACE_SQL_QUERIES_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture SQL query bindings (parameters) in SQL query spans */
        'sql_bindings' => env(
            'SENTRY_TRACE_SQL_BINDINGS_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture where the SQL query originated from on the SQL query spans */
        'sql_origin' => env(
            'SENTRY_TRACE_SQL_ORIGIN_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture views rendered as spans */
        'views' => env(
            'SENTRY_TRACE_VIEWS_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture Livewire components as spans */
        'livewire' => env(
            'SENTRY_TRACE_LIVEWIRE_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture HTTP client requests as spans */
        'http_client_requests' => env(
            'SENTRY_TRACE_HTTP_CLIENT_REQUESTS_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture Redis operations as spans (this enables Redis events in Laravel) */
        'redis_commands' => env(
            'SENTRY_TRACE_REDIS_COMMANDS',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Capture where the Redis command originated from on the Redis command spans */
        'redis_origin' => env(
            'SENTRY_TRACE_REDIS_ORIGIN_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /* Enable tracing for requests without a matching route (404's) */
        'missing_routes' => env(
            'SENTRY_TRACE_MISSING_ROUTES_ENABLED',
            env('SENTRY_TRACE_ENABLED', env('SENTRY_TRACING_ENABLED', false))
        ),

        /*
         * Configures if the performance trace should continue after the response has been
         * sent to the user until the application terminates.
         * This is required to capture any spans that are created after the response has been sent like
         * queue jobs dispatched using `dispatch(...)->afterResponse()` for example.
         */
        'continue_after_response' => env(
            'SENTRY_TRACE_CONTINUE_AFTER_RESPONSE',
            env('SENTRY_TRACE_ENABLED', false)
        ),

        /* Enable the tracing integrations supplied by Sentry (recommended) */
        'default_integrations' => env(
            'SENTRY_TRACE_DEFAULT_INTEGRATIONS_ENABLED',
            env('SENTRY_TRACE_ENABLED', false)
        ),
    ],

];
