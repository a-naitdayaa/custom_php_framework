<?php

namespace Simplex\Tests;

use Simplex\Framework;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\Routing\Exception\RuntimeException;
use Calendar\Controller\LeapYearController;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

class FrameworkTest extends TestCase
{
    public function testControllerResponse(): void
    {
        $matcher = $this->createMock(UrlMatcherInterface::class);
        $matcher->expects($this->once())
            ->method('match')
            ->willREturn([
                '_route' => 'leap-year/{year}',
                'year' => 2020,
                '_controller' => [new LeapYearController(), 'index'],
            ]);

        $matcher->expects($this->once())
            ->method('getContext')
            ->willReturn($this->createMock(RequestContext::class));

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $framework = new Framework($matcher, $controllerResolver, $argumentResolver);
        $response = $framework->handle(new Request());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('The year 2020 is a leap year.', $response->getContent());
    }

    public function testErrorHandling(): void
    {
        $framework = $this->getFrameworkForException(new RuntimeException());
        $response = $framework->handle(new Request());
        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testNotFoundHandling(): void
    {
        $framework = $this->getFrameworkForException(new ResourceNotFoundException());
        $response = $framework->handle(new Request());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function getFrameworkForException($exception): Framework
    {
        $matcher = $this->createMock(UrlMatcherInterface::class);
        $matcher->expects($this->once())
            ->method('match')
            ->willThrowException($exception);

        $matcher->expects($this->once())
            ->method('getContext')
            ->willReturn($this->createMock(RequestContext::class));

        $controllerResolver = $this->createMock(ControllerResolverInterface::class);
        $argumentResolver = $this->createMock(ArgumentResolverInterface::class);

        return new Framework($matcher, $controllerResolver, $argumentResolver);
    }
}
