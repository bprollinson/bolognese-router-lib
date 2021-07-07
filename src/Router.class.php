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

            $parameterValues = [];
            if (!$this->URIMatcher->URIMatchesSpec($request->getURI(), $possibleRoute['request'], $parameterValues))
            {
                continue;
            }

            $methodInvocation = $possibleRoute['methodInvocation'];

            return new MethodInvocation(
                $methodInvocation['hostname'],
                $methodInvocation['namespace'],
                $methodInvocation['class'],
                $methodInvocation['method'],
                $parameterValues,
                $request->getGet(),
                $request->getPost()
            );
        }

        return null;
    }
}
