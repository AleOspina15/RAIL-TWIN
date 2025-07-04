<?php


return [
    'name' => 'AICEDRONE SDI',
    'manifest' => [
        'name' => env('APP_NAME', 'AICEDRONE SDI'),
        'short_name' => 'AICEDRONE SDI',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#ffffff',
        'display' => 'fullscreen',
        'lang' => 'es',
        'orientation' => 'any',
        'status_bar' => 'black',
        'description' => '',
        'icons' => [
            '72x72' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => 'https://aicedrone.tidop.es/img/icons/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => 'https://aicedrone.tidop.es/img/icons/splash-640x1136.png',
            '750x1334' => 'https://aicedrone.tidop.es/img/icons/splash-750x1334.png',
            '828x1792' => 'https://aicedrone.tidop.es/img/icons/splash-828x1792.png',
            '1125x2436' => 'https://aicedrone.tidop.es/img/icons/splash-1125x2436.png',
            '1242x2208' => 'https://aicedrone.tidop.es/img/icons/splash-1242x2208.png',
            '1242x2688' => 'https://aicedrone.tidop.es/img/icons/splash-1242x2688.png',
            '1536x2048' => 'https://aicedrone.tidop.es/img/icons/splash-1536x2048.png',
            '1668x2224' => 'https://aicedrone.tidop.es/img/icons/splash-1668x2224.png',
            '1668x2388' => 'https://aicedrone.tidop.es/img/icons/splash-1668x2388.png',
            '2048x2732' => 'https://aicedrone.tidop.es/img/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            'Inicio de sesión' => [
                'name' => 'Inicio de sesión',
                'url' => 'https://aicedrone.tidop.es/login',
                'description' => 'Inicio de sesión',
                'icons' => [
                    'src' => 'https://aicedrone.tidop.es/img/icons/icon-96x96.png',
                    'purpose' => 'any',
                    'sizes' => '96x96',
                ]
            ],
            'Registro' => [
                'name' => 'Registro',
                'url' => 'https://aicedrone.tidop.es/register',
                'description' => 'Registro',
                'icons' => [
                    'src' => 'https://aicedrone.tidop.es/img/icons/icon-96x96.png',
                    'purpose' => 'any',
                    'sizes' => '96x96',
                ]
            ],
        ],
        'screenshots' => [
            '1280x800' => [
                'path' => 'https://aicedrone.tidop.es/img/screenshots/screenshot_01.png',
                'purpose' => 'any',
                'type' => 'image/png'
            ],
            '750x1334' => [
                'path' => 'https://aicedrone.tidop.es/img/screenshots/screenshot_02.png',
                'purpose' => 'any',
                'type' => 'image/png'
            ],
        ],
        'custom' => [],
        'dir' => 'auto',
        'categories' => ['productivity'],
        'display_override' => ['fullscreen', 'window-controls-overlay'],
        'prefer_related_applications' => false,
        'scope' => "https://aicedrone.tidop.es/",
        "iarc_rating_id" => "7ea46333-21c9-4e6d-be18-3393f40b38e4",
    ]
];


/*
return [
    'name' => 'OPTIM HC',
    'manifest' => [
        'name' => env('APP_NAME', 'My PWA App'),
        'short_name' => 'OPTIM HC',
        'start_url' => '/',
        'background_color' => '#ffffff',
        'theme_color' => '#000000',
        'display' => 'standalone',
        'orientation'=> 'any',
        'status_bar'=> 'black',
        'icons' => [
            '72x72' => [
                'path' => '/images/icons/icon-72x72.png',
                'purpose' => 'any'
            ],
            '96x96' => [
                'path' => '/images/icons/icon-96x96.png',
                'purpose' => 'any'
            ],
            '128x128' => [
                'path' => '/images/icons/icon-128x128.png',
                'purpose' => 'any'
            ],
            '144x144' => [
                'path' => '/images/icons/icon-144x144.png',
                'purpose' => 'any'
            ],
            '152x152' => [
                'path' => '/images/icons/icon-152x152.png',
                'purpose' => 'any'
            ],
            '192x192' => [
                'path' => '/images/icons/icon-192x192.png',
                'purpose' => 'any'
            ],
            '384x384' => [
                'path' => '/images/icons/icon-384x384.png',
                'purpose' => 'any'
            ],
            '512x512' => [
                'path' => '/images/icons/icon-512x512.png',
                'purpose' => 'any'
            ],
        ],
        'splash' => [
            '640x1136' => '/images/icons/splash-640x1136.png',
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
            '1242x2208' => '/images/icons/splash-1242x2208.png',
            '1242x2688' => '/images/icons/splash-1242x2688.png',
            '1536x2048' => '/images/icons/splash-1536x2048.png',
            '1668x2224' => '/images/icons/splash-1668x2224.png',
            '1668x2388' => '/images/icons/splash-1668x2388.png',
            '2048x2732' => '/images/icons/splash-2048x2732.png',
        ],
        'shortcuts' => [
            [
                'name' => 'Shortcut Link 1',
                'description' => 'Shortcut Link 1 Description',
                'url' => '/shortcutlink1',
                'icons' => [
                    "src" => "/images/icons/icon-72x72.png",
                    "purpose" => "any"
                ]
            ],
            [
                'name' => 'Shortcut Link 2',
                'description' => 'Shortcut Link 2 Description',
                'url' => '/shortcutlink2'
            ]
        ],
        'custom' => []
    ]
];
*/
