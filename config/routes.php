<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    'PLUGIN_NAME',
    ['path' => '/PLUGIN_NAME'],
    function (RouteBuilder $routes) {
        $routes->fallbacks('DashedRoute');
    }
);
