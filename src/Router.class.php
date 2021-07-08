<?php

require_once(dirname(__FILE__) . '/URIMatcher.class.php');
require_once('vendor/bprollinson/bolognese-router-api/src/Request.class.php');
require_once('vendor/bprollinson/bolognese-controller-api/src/MethodInvocation.class.php');

class Router
{
    private $routesArray;
    private $URIMatcher;

    public function __construct(array $routesArray, URIMatcher $URIMatcher)
    {
        $this->routesArray = $routesArray;
        $this->URIMatcher = $URIMatcher;
    }

    public function route(Request $request)
    {
        foreach ($this->routesArray as $possibleRoute)
        {
            $methodInvocation = $this->routeForPossibleRoute($request, $possibleRoute);
            if ($methodInvocation !== null)
            {
                return $methodInvocation;
            }
        }

        return null;
    }

    private function routeForPossibleRoute(Request $request, array $possibleRoute): ?MethodInvocation
    {
        $URIMatchResult = $this->matchPossibleRoute($request, $possibleRoute);
        if (!$URIMatchResult->matches())
        {
            return null;
        }

        $methodInvocation = $possibleRoute['methodInvocation'];

        return new MethodInvocation(
            $methodInvocation['hostname'],
            $methodInvocation['namespace'],
            $methodInvocation['class'],
            $methodInvocation['method'],
            $URIMatchResult->getParameterValues(),
            $request->getGet(),
            $request->getPost()
        );
    }

    private function matchPossibleRoute(Request $request, array $possibleRoute): ?URIMatchResult
    {
        if ($request->getMethod() != $possibleRoute['request']['method'])
        {
            return new URIMatchResult(false);
        }

        return $this->URIMatcher->matchAgainstSpec($request->getURI(), $possibleRoute['request']);
    }
}
