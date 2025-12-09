<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/admin' => [[['_route' => 'admin', '_controller' => 'App\\Controller\\Admin\\DashboardController::index'], null, null, null, false, false, null]],
        '/admin/usuario' => [[['_route' => 'admin_usuario_index', '_controller' => 'App\\Controller\\Admin\\UsuarioCrudController::index'], null, ['GET' => 0], null, false, false, null]],
        '/admin/usuario/new/form' => [[['_route' => 'admin_usuario_new', '_controller' => 'App\\Controller\\Admin\\UsuarioCrudController::new'], null, ['GET' => 0, 'POST' => 1], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/admin/usuario/([^/]++)(?'
                    .'|(*:68)'
                    .'|/(?'
                        .'|edit(*:83)'
                        .'|delete(*:96)'
                    .')'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        68 => [[['_route' => 'admin_usuario_detail', '_controller' => 'App\\Controller\\Admin\\UsuarioCrudController::detail'], ['id'], ['GET' => 0], null, false, true, null]],
        83 => [[['_route' => 'admin_usuario_edit', '_controller' => 'App\\Controller\\Admin\\UsuarioCrudController::edit'], ['id'], ['GET' => 0, 'POST' => 1], null, false, false, null]],
        96 => [
            [['_route' => 'admin_usuario_delete', '_controller' => 'App\\Controller\\Admin\\UsuarioCrudController::delete'], ['id'], ['GET' => 0], null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
