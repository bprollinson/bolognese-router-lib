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
            if ($request->getMethod() != $possibleRoute['request']['method'])
            {
                continue;
            }

            $URIMatchResult = $this->URIMatcher->matchAgainstSpec($request->getURI(), $possibleRoute['request']);
            if (!$URIMatchResult->matches())
            {
                continue;
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

        return null;
    }
}
