<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    */
    'title' => 'Agenda Online',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    'use_route_url' => false,
    'dashboard_url' => 'agenda',  // Cambiar 'home' por 'agenda'
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    */
    'menu' => [
        // Sidebar items:
        [
            'text' => 'Calendario',
            'url'  => '/agenda',
            'icon' => 'far fa-calendar-alt',
        ],
        [
            'text' => 'Nuevo Evento',
            'url'  => '/agenda/events/create',
            'icon' => 'far fa-plus-square',
        ],
        [
            'text' => 'Administración',
            'icon' => 'fas fa-fw fa-shield-alt',
            'can'  => 'manage users',
            'submenu' => [
                [
                    'text' => 'Dashboard',
                    'url'  => '/admin/dashboard',
                    'icon' => 'fas fa-fw fa-tachometer-alt',
                ],
                [
                    'text' => 'Eventos Globales',
                    'url'  => '/admin/events/create',
                    'icon' => 'fas fa-fw fa-bullhorn',
                ],
                [
                    'text' => 'Gestión de Usuarios',
                    'url'  => '/admin/users',
                    'icon' => 'fas fa-fw fa-users',
                ],
                [
                    'text' => 'Gestión de Roles',
                    'url'  => '/admin/roles',
                    'icon' => 'fas fa-fw fa-user-shield',
                    'can'  => 'manage roles',
                ],
            ],
        ],
    ],

    // ... el resto de la configuración se mantiene igual
];