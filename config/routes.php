<?php declare(strict_types = 1);

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::prefix('Admin', function (RouteBuilder $routes): void {
    $routes->plugin('StateMachine', ['path' => '/state-machine'], function (RouteBuilder $routes): void {
        $routes->connect('/', ['controller' => 'StateMachine', 'action' => 'index'], ['routeClass' => DashedRoute::class]);

        $routes->fallbacks(DashedRoute::class);
    });
});
