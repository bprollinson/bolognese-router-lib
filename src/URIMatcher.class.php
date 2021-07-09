<?php

require_once(dirname(__FILE__) . '/URIMatchResult.class.php');

class URIMatcher
{
    public function matchAgainstSpec(string $uri, array $requestSpecification): URIMatchResult
    {
        $variableNames = $this->extractVariableNamesFromURISpec($requestSpecification['uri']);

        $URISpecification = "#^{$requestSpecification['uri']}$#";
        foreach ($variableNames as $variableName)
        {
            $variableRegularExpression = $this->buildVariableRegularExpression($variableName, $requestSpecification['params']);
            if ($variableRegularExpression === null)
            {
                return new URIMatchResult(false);
            }

            $URISpecification = str_replace('{' . $variableName . '}', "($variableRegularExpression)", $URISpecification);
        }

        return $this->buildMatchResult($URISpecification, $uri, $variableNames);
    }

    private function extractVariableNamesFromURISpec(string $URISpec): array
    {
        $variableMatches = [];
        preg_match_all("#{.*?}#", $URISpec, $variableMatches);

        return array_map(function ($variableMatch) {
            return substr($variableMatch, 1, -1);
        }, $variableMatches[0]);
    }

    private function buildVariableRegularExpression(string $variableName, array $params): ?string
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

    private function buildMatchResult(string $URISpecification, string $uri, array $variableNames): URIMatchResult
    {
        $matches = [];
        $result = preg_match($URISpecification, $uri, $matches) === 1;

        $parameterValues = [];
        if ($result)
        {
            array_shift($matches);
            $parameterValues = array_combine($variableNames, $matches);
        }

        return new URIMatchResult($result, $parameterValues);
    }
}
