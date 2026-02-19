<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();
$requestStack = new RequestStack();
$routes = include __DIR__ . '/../src/app.php';

$context = new RequestContext();
//$context->fromRequest($request);

//$compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
$matcher = new UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

$dispatcher = new EventDispatcher();
//$dispatcher->addSubscriber(new ContentLengthListener());
//$dispatcher->addSubscriber(new GoogleListener());
$dispatcher->addSubscriber(new RouterListener($matcher, $requestStack));

$errorHandler = function (FlattenException $exception): Response {
    $msg = 'Something went wrong! (' . $exception->getMessage() . ')';

    return new Response($msg, $exception->getStatusCode());
};
$dispatcher->addSubscriber(new ErrorListener($errorHandler));

$listener = new ErrorListener(
    'Calendar\Controller\ErrorController::exception'
);
$dispatcher->addSubscriber($listener);

//adding http caching support
$framework = new Simplex\Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
$framework = new HttpCache($framework, new Store(__DIR__ . '/../cache'));

$response = $framework->handle($request);
$response->send();
