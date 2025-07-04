<?php
config([
    'adminlte.menu' => [
        [
            'text'        => $nombre,
            'icon'        => 'fa-solid fa-toolbox elemento-sidebar-principal',
            'can' => 'Proyectos',
            'submenu' => [
                [
                    'text'    => 'Gestor de archivos',
                    'icon'    => 'fa-solid fa-folder-open elemento-sidebar-secundario',
                    'url'  => 'gestorDeArchivos/'.$id
                ],
                [
                    'text'        => 'Volver a Proyectos',
                    'url'         => 'proyectos',
                    'icon'        => 'fa-solid fa-arrow-left elemento-sidebar-secundario',
                ]
            ],
        ],
        [
            'text' => 'Visor',
            'url'  => 'visor',
            'icon'        => 'fa-solid fa-map elemento-sidebar-principal'
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
]);

