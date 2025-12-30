<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Domain Name
    |--------------------------------------------------------------------------
    |
    | This value specifies the domain name for the API. When set, API routes
    | will only respond to requests made to this specific domain, allowing
    | you to host your API on a separate subdomain from your main application.
    |
    */

    'domain_name' => env('API_DOMAIN_NAME'),

    /*
    |--------------------------------------------------------------------------
    | API Path Prefix
    |--------------------------------------------------------------------------
    |
    | This value defines the URL path prefix for all API routes. All API
    | endpoints will be accessible under this path, making it easy to
    | distinguish API routes from your web application routes.
    |
    */

    'path' => env('API_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | These values control the pagination behavior for API responses. The
    | default determines how many items are returned when no limit is
    | specified, while min and max set the allowed boundaries.
    |
    */

    'pagination' => [
        'per_page_default' => 50,
        'per_page_max' => 200,
        'per_page_min' => 10,
    ],

];
