<?php

class URIMatcher
{
    public function URIMatchesSpec($uri, $requestSpecification, &$parameterValues)
    {
        $variableMatches = [];
        preg_match_all("#{.*?}#", $requestSpecification['uri'], $variableMatches);
        $URISpecification = "#^{$requestSpecification['uri']}$#";
        $variableNames = [];
        foreach ($variableMatches[0] as $variableMatch)
        {
            $variableName = substr($variableMatch, 1, -1);
            $variableNames[] = $variableName;
            $variableRegularExpression = $this->buildVariableRegularExpression($variableName, $requestSpecification['params']);
            if ($variableRegularExpression === null)
            {
                return false;
            }

            $URISpecification = str_replace($variableMatch, "($variableRegularExpression)", $URISpecification);
        }

        $matches = [];
        $result = preg_match($URISpecification, $uri, $matches) === 1;

        if ($result)
        {
            array_shift($matches);
            $parameterValues = array_combine($variableNames, $matches);
        }

        return $result;
    }

    private function buildVariableRegularExpression($variableName, $params)
    {
        $matchingParams = array_filter($params, function($param) use ($variableName) {
            return $param['name'] == $variableName;
        });

        if (count($matchingParams) == 0)
        {
            return null;
        }

        $matchingParams = array_values($matchingParams);

        switch ($matchingParams[0]['type'])
        {
            case 'natural':
                return '[0-9]*';
            case 'alpha':
                return '[a-zA-Z]*';
            default:
                return null;
        }
    }
}
