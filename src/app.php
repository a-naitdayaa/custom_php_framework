<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

function is_leap_year(?int $year = null): bool
{
    if ($year === null) {
        $year = (int) date('Y');
    }
    return ($year % 4 === 0 && $year % 100 !== 0) || ($year % 400 === 0);
}

$routes = new RouteCollection();
$routes->add('hello', new Route('/hello/{name}', [
    'name' => 'World',
    '_controller' => 'render_template'
]));
$routes->add('bye', new Route('/bye'));
$routes->add('leap-year', new Route('/leap-year/{year}', [
    'year' => null,
    '_controller' => function (Request $request): Response {
        $year = $request->attributes->get('year');
        $isLeap = is_leap_year($year);
        if ($isLeap) {
            return new Response(sprintf('The year %d is a leap year.', $year));
        }
        return new Response(sprintf('The year %d is not a leap year.', $year));
    }
]));

return $routes;
