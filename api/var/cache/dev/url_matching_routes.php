<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/api/doc' => [[['_route' => 'app.swagger_ui', '_controller' => 'nelmio_api_doc.controller.swagger'], null, null, null, false, false, null]],
        '/api/doc.json' => [[['_route' => 'app.swagger_json', '_controller' => 'nelmio_api_doc.controller.swagger_json'], null, null, null, false, false, null]],
        '/api/v1/usuarios' => [
            [['_route' => 'api_usuarios_index', '_controller' => 'App\\Controller\\UsuarioController::index'], null, ['GET' => 0], null, false, false, null],
            [['_route' => 'api_usuarios_create', '_controller' => 'App\\Controller\\UsuarioController::create'], null, ['POST' => 0], null, false, false, null],
        ],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/api/v1/usuarios/([^/]++)(?'
                    .'|(*:70)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        70 => [
            [['_route' => 'api_usuarios_show', '_controller' => 'App\\Controller\\UsuarioController::show'], ['id'], ['GET' => 0], null, false, true, null],
            [['_route' => 'api_usuarios_update', '_controller' => 'App\\Controller\\UsuarioController::update'], ['id'], ['PUT' => 0, 'PATCH' => 1], null, false, true, null],
            [['_route' => 'api_usuarios_delete', '_controller' => 'App\\Controller\\UsuarioController::delete'], ['id'], ['DELETE' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
