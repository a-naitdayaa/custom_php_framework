<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$routes = new RouteCollection();
$routes->add('leap-year', new Route('/leap-year/{year}', [
    'year' => null,
    '_controller' => 'Calendar\Controller\LeapYearController::index',
]));

return $routes;
