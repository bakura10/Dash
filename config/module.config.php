<?php

use Dash\Router\Http\Parser\ParserManager as HttpParserManager;
use Dash\Router\Http\Route\RouteManager as HttpRouteManager;
use Dash\Router\Http\Router as HttpRouter;
use Dash\Router\Http\RouterFactory as HttpRouterFactory;

return [
    'service_manager' => [
        'invokables' => [
            HttpParserManager::class => HttpParserManager::class,
            HttpRouteManager::class  => HttpRouteManager::class
        ],
        'factories' => [
            HttpRouter::class => HttpRouterFactory::class
        ],
    ],
];
