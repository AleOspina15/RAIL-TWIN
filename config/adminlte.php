<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'RAIL TWIN',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => null,
    'logo_img' => 'img/logo_rt.png',
    'logo_img_class' => 'brand-image',
    'logo_img_xl' => 'img/logo_rt.png',
    'logo_img_xl_class' => 'brand-image-lg',
    'logo_img_alt' => 'RT Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'img/logo_rt.png',
            'alt' => 'RAIL TWIN',
            'class' => '',
            'width' => null,
            'height' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'img/logo_rt.png',
            'alt' => '',
            'effect' => 'animation__shake',
            'width' => null,
            'height' => 60,
        ],
    ],

    /*1
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-cyan elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-dark navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fa-lg fa-solid fa-layer-group',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Mix
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Mix option for the admin panel.
    |
    | For detailed instructions you can look the laravel mix section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'enabled_laravel_mix' => false,
    'laravel_mix_css_path' => 'css/app.css',
    'laravel_mix_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        [
            'text'        => 'Resiliencia Estructural',
            'icon'        => 'fa fa-cubes elemento-sidebar-principal', 
            'submenu' =>[
                [
                    'text'        => 'Proyectos',
                    'icon'        => 'fa-solid fa-toolbox elemento-sidebar-secundario',
                    'can' => 'Proyectos',
                    'url'  => 'proyectos',
                ],
                [
                    'text' => 'Visor',
                    'url'  => 'visor',
                    'icon'        => 'fa-solid fa-map elemento-sidebar-secundario'
                ],
                [
                    'text' => 'BIM vs. As Built',
                    'url'  => 'ueview',
                    'icon'        => 'fa-solid fa-train elemento-sidebar-secundario'
                ],
                [
                    'text' => 'Simulación Inundación',
                    'url'  => 'inundacion',
                    'icon'        => 'fa-solid fa-water elemento-sidebar-secundario'
                ],
                [
                    'text' => 'Simulación Incendio',
                    'url'  => 'incendio',
                    'icon'        => 'fa-solid fa-fire elemento-sidebar-secundario'
                ],
            ],
        ],
        [
            'text'        => 'Resiliencia Energética',
            'icon'        => 'fa fa-solid fa-bolt elemento-sidebar-principal', 
            'url'  => 'visor2',
            /*'submenu' => [
                [
                    'text'        => 'Avila - Madrid',
                    'icon'        => 'fa-solid fa-toolbox elemento-sidebar-secundario',
                    'url'  => 'visor2',
                ],
                [
                    'text'        => 'Avila - Valladolid',
                    'icon'        => 'fa-solid fa-toolbox elemento-sidebar-secundario',
                    'url'  => 'visor2',
                ],
                [
                    'text'        => 'Burgos - Miranda del Ebro',
                    'icon'        => 'fa-solid fa-toolbox elemento-sidebar-secundario',
                    'url'  => 'visor2',
                ],
            ]*/
            
        ],

        /*
        [
            'text'        => 'Catálogo GIS',
            'icon'        => 'fas fa-fw fa-layer-group',
            'submenu' => [
                [
                    'text'    => 'Visor 2D',
                    'icon'    => 'fas fa-fw fa-map',
                    'url'  => 'visor2D',
                    'classes' => 'pl-4 text-white',
                ],
                [
                    'text'        => 'Capas',
                    'url'         => 'capas',
                    'icon'        => 'fas fa-fw fa-square',
                    'active' => ['capas','capas/create','regex:@^capas/[0-9]+/edit$@','regex:@^capas/[0-9]+@'],
                    'classes' => 'pl-4 text-white',
                ],
                [
                    'text'        => 'Grupos',
                    'url'         => 'grupos',
                    'icon'        => 'fas fa-fw fa-clone',
                    'active' => ['grupos','grupos/create','regex:@^grupos/[0-9]+/edit$@'],
                    'classes' => 'pl-4 text-white',
                ],
            ],
            'can' => 'Capas',
        ],
        */
        [
            'text'        => 'Usuarios',
            'icon'        => 'fa-solid fa-users elemento-sidebar-principal',
            'submenu' => [
                [
                    'text'        => 'Permisos',
                    'url'         => 'permissions',
                    'icon'        => 'fa-solid fa-lock elemento-sidebar-secundario',
                    'active' => ['permissions','permissions/create','regex:@^permissions/[0-9]+/edit$@']
                ],
                [
                    'text'        => 'Roles',
                    'url'         => 'roles',
                    'icon'        => 'fa-solid fa-briefcase elemento-sidebar-secundario',
                    'active' => ['roles','roles/create','regex:@^roles/[0-9]+/edit$@']
                ],
                [
                    'text'        => 'Usuarios',
                    'url'         => 'users',
                    'icon'        => 'fa-solid fa-user elemento-sidebar-secundario',
                    'active' => ['users','users/create','regex:@^users/[0-9]+/edit$@']
                ],
            ],
            'can' => 'Usuarios',
        ],
        [
            'text'        => 'Gestor de archivos',
            'url'         => 'fileManager',
            'icon'        => 'fa-solid fa-folder-open elemento-sidebar-principal',
            'can' => 'Gestor de archivos'
        ],
        /*
        [
            'text'        => 'Ajustes',
            'icon'        => 'fa-solid fa-gear elemento-sidebar-principal',
            'submenu' => [
                [
                    'text'        => 'Actualización OSM',
                    'url'         => 'osmUpdates',
                    'icon'        => 'fa-solid fa-route elemento-sidebar-secundario',
                    'active' => ['osmUpdates'],
                    'can' => 'Proyectos'
                ],
                [
                    'text'        => 'Explotaciones de áridos',
                    'url'         => 'explotaciones',
                    'icon'        => 'fa-solid fa-person-digging elemento-sidebar-secundario',
                    'active' => ['explotaciones','regex:@^editarExplotacion/[0-9]+@'],
                    'can' => 'Proyectos'
                ],
                [
                    'text'        => 'Vehículos',
                    'url'         => 'vehiculos',
                    'icon'        => 'fa-solid fa-truck elemento-sidebar-secundario',
                    'active' => ['vehiculos','regex:@^editarVehiculo/[0-9]+@'],
                    'can' => 'Proyectos'
                ],
                [
                    'text'        => 'Vías',
                    'url'         => 'vias',
                    'icon'        => 'fa-solid fa-road elemento-sidebar-secundario',
                    'active' => ['vias','regex:@^editarVia/[0-9]+@'],
                    'can' => 'Proyectos'
                ],
            ],
            'can' => 'Proyectos',
        ],
        */
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/datatables/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'datatablesPlugins' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/datatables-plugins/buttons/js/dataTables.buttons.min.js',
                ]
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
        'moment' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/moment/moment-with-locales.js',
                ],
            ],
        ],
        'daterangepicker' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/daterangepicker/daterangepicker.js',
                ],
            ],
        ],
        'jqueryUi' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/jquery-ui/jquery-ui.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/jquery-ui/jquery-ui.min.js',
                ],
            ],
        ],
        'Winbox' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => true,
                    'location' => 'vendor/winbox/dist/css/winbox.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/winbox/dist/js/winbox.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
