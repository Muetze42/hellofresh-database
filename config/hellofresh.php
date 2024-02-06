<?php

return [
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
        'recipes' => [
            'header' => 'c_fit,f_auto,fl_lossy,h_1100,q_auto,w_2600',
            'preview' => 'c_fill,f_auto,fl_lossy,h_202,q_auto,w_360',
        ],
        'ingredient' => [
            'image' => 'w_96,q_auto,f_auto,c_limit,fl_lossy',
            // w_96,q_auto,f_auto,c_limit,fl_lossy
        ],
        'steps' => [
            'image' => 'f_auto,fl_lossy,q_auto,w_340',
        ],
    ],
];
