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
    public function routerReturnsFirstMethodInvocationWhenMultipleRoutesMatch()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ],
                'methodInvocation' => [
                    'hostname' => 'controller1',
                    'namespace' => null,
                    'class' => 'MyController1',
                    'method' => 'myMethod1'
                ]
            ],
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ],
                'methodInvocation' => [
                    'hostname' => 'controller2',
                    'namespace' => null,
                    'class' => 'MyController2',
                    'method' => 'myMethod2'
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
 
        $expectedMethodInvocation = new MethodInvocation('controller1', null, 'MyController1', 'myMethod1', [], [], []);
        $methodInvocation = $router->route(new Request('GET', '/path', [], []));
        $this->assertEquals($expectedMethodInvocation, $methodInvocation);
    }

    /**                                                                         
     * @test                                                                    
     */                                                                         
    public function routerReturnsSubsequentMethodInvocationWhenFirstRouteDoesntMatch()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/otherpath'
                ],
                'methodInvocation' => [
                    'hostname' => 'controller1',
                    'namespace' => null,
                    'class' => 'MyController1',
                    'method' => 'myMethod1'
                ]
            ],
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path'
                ],
                'methodInvocation' => [
                    'hostname' => 'controller2',
                    'namespace' => null,
                    'class' => 'MyController2',
                    'method' => 'myMethod2'
                ]
            ]
        ];
        $URIMatcher = $this->createMock(URIMatcher::class);
        $URIMatcher->expects($this->exactly(2))
            ->method('matchAgainstSpec')
            ->withConsecutive([
                '/path',
                [
                    'method' => 'GET',
                    'uri' => '/otherpath'
                ]
            ], [
                '/path',
                [
                    'method' => 'GET',
                    'uri' => '/path'
                ]
            ])
            ->willReturnOnConsecutiveCalls(
                new URIMatchResult(false),
                new URIMatchResult(true)
            );
        $router = new Router($routesArray, $URIMatcher);
 
        $expectedMethodInvocation = new MethodInvocation('controller2', null, 'MyController2', 'myMethod2', [], [], []);
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
    public function routerReturnsMethodInvocationWithURLParamsWhenRouteMatches()
    {
        $routesArray = [
            [
                'request' => [
                    'method' => 'GET',
                    'uri' => '/path/{url_key1}/{url_key2}'
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
        $urlParams = [
            'url_key1' => 'url_value1',
            'url_key2' => 'url_value2'
        ];
        $URIMatcher->expects($this->once())
            ->method('matchAgainstSpec')
            ->with(
                '/path/url_value1/url_value2',
                [
                    'method' => 'GET',
                    'uri' => '/path/{url_key1}/{url_key2}'
                ]
            )
            ->willReturn(new URIMatchResult(true, $urlParams));
        $router = new Router($routesArray, $URIMatcher); 

        $expectedMethodInvocation = new MethodInvocation('controller', null, 'MyController', 'myMethod', $urlParams, [], []);
        $methodInvocation = $router->route(new Request('GET', '/path/url_value1/url_value2', [], []));
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
