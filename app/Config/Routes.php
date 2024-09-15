<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('dsahboard', static function($routes){
    $routes->get('/' , 'admin\Dsahboard::index');
});
        