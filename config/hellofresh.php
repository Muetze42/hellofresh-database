<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HelloFresh CDN
    |--------------------------------------------------------------------------
    |
    | Base URL and bucket name for HelloFresh's Cloudinary CDN.
    |
    */
    'cdn' => [
        'base_url' => 'https://img.hellofresh.com',
        'bucket' => 'hellofresh_s3',
    ],

    /*
    |--------------------------------------------------------------------------
    | HelloFresh Assets
    |--------------------------------------------------------------------------
    |
    | These values are used to generate the URLs to the various assets.
    | HelloFresh uses the Cloudinary service where you can transform the assets
    | via the URL: https://cloudinary.com/documentation/image_transformations
    |
    */
    'assets' => [
        'recipe' => [
            'header' => 'c_fit,f_auto,fl_lossy,h_1100,q_auto,w_2600',
            'card' => 'w_470,q_auto,f_auto,c_limit,fl_lossy',
        ],
        'ingredient' => [
            'thumbnail' => 'w_96,q_auto,f_auto,c_limit,fl_lossy',
        ],
        'step' => [
            'image' => 'f_auto,fl_lossy,q_auto,w_340',
        ],
    ],
];
