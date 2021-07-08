<?php

require_once(dirname(__FILE__) . '/../src/URIMatcher.class.php');
require_once(dirname(__FILE__) . '/../src/Router.class.php');
require_once(dirname(__FILE__) . '/../src/URIMatchResult.class.php');
require_once(dirname(__FILE__) . '/../vendor/bprollinson/bolognese-router-api/src/Request.class.php');
require_once('vendor/bprollinson/bolognese-controller-api/src/MethodInvocation.class.php');

use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * @test
     */
    public function routeReturnsNullWhenNoRoutesDeined()
    {
        $routesArray = [];
        $URIMatcher = $this->createMock(URIMatcher::class);
        $router = new Router($routesArray, $URIMatcher);

        $methodInvocation = $router->route(new Request('GET', '/path', [], []));
        $this->assertNull($methodInvocation);
    }

    /**                                                                         
     * @test                                                                    
     */                                                                         
    public function routerReturnsMethodInvocationWhenRouteMatches()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ],
                'methodInvocation' => [
                    'hostname' => 'controller',
                    'namespace' => null,
                    'class' => 'MyController',
                    'method' => 'myMethod'
                ]
            ]
        ];
        $URIMatcher = $this->createMock(URIMatcher::class);
        $URIMatcher->expects($this->once())
            ->method('matchAgainstSpec')
            ->with(
                '/path',
                [
                    'method' => 'GET',
                    'uri' => '/path'
                ]
            )
            ->willReturn(new URIMatchResult(true));
        $router = new Router($routesArray, $URIMatcher);
 
        $expectedMethodInvocation = new MethodInvocation('controller', null, 'MyController', 'myMethod', [], [], []);
        $methodInvocation = $router->route(new Request('GET', '/path', [], []));
        $this->assertEquals($expectedMethodInvocation, $methodInvocation);
    }

    /**                                                                         
     * @test                                                                    
     */                                                                         
    public function routerReturnsMethodInvocationWithGetAndPostParamsWhenRouteMatches()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ],
                'methodInvocation' => [
                    'hostname' => 'controller',
                    'namespace' => null,
                    'class' => 'MyController',
                    'method' => 'myMethod'
                ]
            ]
        ];
        $URIMatcher = $this->createMock(URIMatcher::class);
        $URIMatcher->expects($this->once())
            ->method('matchAgainstSpec')
            ->with(
                '/path',
                [
                    'method' => 'GET',
                    'uri' => '/path'
                ]
            )
            ->willReturn(new URIMatchResult(true));
        $router = new Router($routesArray, $URIMatcher);
 
        $getParams = [
            'get_key1' => 'get_value1',
            'get_key2' => 'get_value2'
        ];
        $postParams = [
            'post_key1' => 'post_value1',
            'post_key2' => 'post_value2'
        ];
        $expectedMethodInvocation = new MethodInvocation('controller', null, 'MyController', 'myMethod', [], $getParams, $postParams);
        $methodInvocation = $router->route(new Request('GET', '/path', $getParams, $postParams));
        $this->assertEquals($expectedMethodInvocation, $methodInvocation);
    }

    /**
     * @test
     */
    public function routerReturnsNullWhenRequestTypeDoesntMatch()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ]
            ]
        ];
        $URIMatcher = $this->createMock(URIMatcher::class);
        $router = new Router($routesArray, $URIMatcher);

        $methodInvocation = $router->route(new Request('POT', '/path', [], []));
        $this->assertNull($methodInvocation);
    }


    /**
     * @test
     */
    public function routerReturnsNullWhenRouteDoesntMatch()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ]
            ]
        ];
        $URIMatcher = $this->createMock(URIMatcher::class);
        $URIMatcher->expects($this->once())
            ->method('matchAgainstSpec')
            ->with(
                '/otherpath',
                [
                    'method' => 'GET',
                    'uri' => '/path'
                ]
            )
            ->willReturn(new URIMatchResult(false)); 
        $router = new Router($routesArray, $URIMatcher);

        $methodInvocation = $router->route(new Request('GET', '/otherpath', [], []));
        $this->assertNull($methodInvocation);
    }
}
