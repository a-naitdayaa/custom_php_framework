<?php

namespace Simplex;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Framework implements HttpKernelInterface
{
    public function __construct(
        private EventDispatcher $dispatcher,
        private UrlMatcherInterface $urlMatcher,
        private ControllerResolverInterface $controllerResolver,
        private ArgumentResolverInterface $argumentResolver
    ) {}

    public function handle(
        Request $request,
        int $type = HttpKernelInterface::MAIN_REQUEST,
        bool $catch = true
    ): Response {
        $this->urlMatcher->getContext()->fromRequest($request);
        try {
            $request->attributes->add($this->urlMatcher->match($request->getPathInfo()));

            $controller = $this->controllerResolver->getController($request);
            $arguments = $this->argumentResolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (ResourceNotFoundException $e) {
            $response = new Response('Not Found', 404);
        } catch (\Exception $e) {
            $response = new Response('An error occurred: ' . $e->getMessage(), 500);
        }

        $this->dispatcher->dispatch(new ResponseEvent($request, $response), 'response');
        return $response;
    }
}
