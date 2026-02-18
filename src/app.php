<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

function is_leap_year(?int $year = null): bool
{
    if ($year === null) {
        $year = (int) date('Y');
    }
    return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
}

$routes = new RouteCollection();
/*$routes->add('hello', new Route('/hello/{name}', [
    'name' => 'World',
    '_controller' => 'render_template'
]));
$routes->add('bye', new Route('/bye'));*/
$routes->add('leap-year', new Route('/leap-year/{year}', [
    'year' => null,
    '_controller' => 'App\\Controllers\\LeapYearController::index',
]));

return $routes;
