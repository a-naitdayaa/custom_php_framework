<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\CompiledUrlMatcher;
use Symfony\Component\Routing\Matcher\Dumper\CompiledUrlMatcherDumper;

function render_template(Request $request): Response
{
    //gets all GET query parameters as an array and extracts them into local variables.
    //the flag EXTR_SKIP will prevent overwriting existing variables
    extract($request->attributes->all(), EXTR_SKIP);

    //turn on output buffering - anything printed/echoed from this point won't be sent to the browser immediately, it gets captured in a buffer instead.
    ob_start();

    //the included file's output will be passed to Symfony's Response object.
    //$_route comes directly from the matched route definition in app.php
    include sprintf(__DIR__ . '/../src/pages/%s.php', $_route);

    //get the content from the output buffer and clean it
    return new Response(ob_get_clean());
}

$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/app.php';

$context = new RequestContext();
$context->fromRequest($request);

$compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
$matcher = new CompiledUrlMatcher($compiledRoutes, $context);

try {
    $request->attributes->add($matcher->match($request->getPathInfo()));
    $response = call_user_func('render_template', $request);
} catch (ResourceNotFoundException $e) {
    $response = new Response('Not Found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred: ', 500);
}

$response->send();
