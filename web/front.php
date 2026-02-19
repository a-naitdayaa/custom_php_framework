<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Simplex\GoogleListener;
use Simplex\ContentLengthListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\HttpCache\Store;

$dispatcher = new EventDispatcher();
$dispatcher->addSubscriber(new ContentLengthListener());
$dispatcher->addSubscriber(new GoogleListener());


$request = Request::createFromGlobals();
$routes = include __DIR__ . '/../src/app.php';

$context = new RequestContext();
$context->fromRequest($request);

//$compiledRoutes = (new CompiledUrlMatcherDumper($routes))->getCompiledRoutes();
$matcher = new UrlMatcher($routes, $context);

$controllerResolver = new ControllerResolver();
$argumentResolver = new ArgumentResolver();

//adding http caching support
$framework = new Simplex\Framework($dispatcher, $matcher, $controllerResolver, $argumentResolver);
$framework = new HttpCache($framework, new Store(__DIR__ . '/../cache'));

$response = $framework->handle($request);
$response->send();
