<?php

class URIMatchResult
{
    private $matches;
    private $parameterValues;

    public function __construct(bool $matches, array $parameterValues = [])
    {
        $this->matches = $matches;
        $this->parameterValues = [];
    }

    public function matches()
    {
        return $this->matches;
    }

    public function getParameterValues()
    {
        return $this->parameterValues;
    }
}
