<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin(
    '{{plugin-name}}',
    ['path' => '/{{plugin-name}}'],
    function (RouteBuilder $routes) {
        $routes->fallbacks('DashedRoute');
    }
);
