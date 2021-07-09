<?php

require_once(dirname(__FILE__) . '/../src/URIMatcher.class.php');
require_once(dirname(__FILE__) . '/../src/URIMatchResult.class.php');

use PHPUnit\Framework\TestCase;

class URIMatcherTest extends TestCase
{
    /**
     * @test
     */
    public function matchAgainstSpecReturnsPositiveResult()
    {
        $URIMatcher = new URIMatcher();
        $spec = [
            'uri' => '/path'
        ];

        $expectedURIMatchResult = new URIMatchResult(true);
        $URIMatchResult = $URIMatcher->matchAgainstSpec('/path', $spec);
        $this->assertEquals($expectedURIMatchResult, $URIMatchResult);
    }

    /**
     * @test
     */
    public function matchAgainstSpecReturnsNegativeResult()
    {
        $URIMatcher = new URIMatcher();
        $spec = [
            'uri' => '/path'
        ];

        $expectedURIMatchResult = new URIMatchResult(false);
        $URIMatchResult = $URIMatcher->matchAgainstSpec('/otherpath', $spec);
        $this->assertEquals($expectedURIMatchResult, $URIMatchResult);
    }

    /**
     * @test
     */
    public function matchAgainstSpecReturnsPositiveResultForSpecWithNaturalNumberParameter()
    {
        $URIMatcher = new URIMatcher();
        $spec = [
            'uri' => '/path/{param}',
            'params' => [
                [
                    'name' => 'param',
                    'type' => 'natural'
                ]
            ]
        ];

        $parameterValues = [
            'param' => 1
        ];
        $expectedURIMatchResult = new URIMatchResult(true, $parameterValues);
        $URIMatchResult = $URIMatcher->matchAgainstSpec('/path/1', $spec);
        $this->assertEquals($expectedURIMatchResult, $URIMatchResult);
    }

    /**
     * @test
     */
    public function matchAgainstSpecReturnsPositiveResultForSpecWithAlphabeticParameter()
    {
        $URIMatcher = new URIMatcher();
        $spec = [
            'uri' => '/path/{param}',
            'params' => [
                [
                    'name' => 'param',
                    'type' => 'alpha'
                ]
            ]
        ];

        $parameterValues = [
            'param' => 'value'
        ];
        $expectedURIMatchResult = new URIMatchResult(true, $parameterValues);
        $URIMatchResult = $URIMatcher->matchAgainstSpec('/path/value', $spec);
        $this->assertEquals($expectedURIMatchResult, $URIMatchResult);
    }

    /**
     * @test
     */
    public function matchAgainstSpecReturnsNegativeResultForUnknownParameterType()
    {
        $URIMatcher = new URIMatcher();
        $spec = [
            'uri' => '/path/{param}',
            'params' => [
                [
                    'name' => 'param',
                    'type' => 'unknowntype'
                ]
            ]
        ];

        $expectedURIMatchResult = new URIMatchResult(false);
        $URIMatchResult = $URIMatcher->matchAgainstSpec('/path/value', $spec);
        $this->assertEquals($expectedURIMatchResult, $URIMatchResult);
    }

    /**
     * @test
     */
    public function matchAgainstSpecReturnsNegativeResultWhenParameterIsMissingDeclaration()
    {
        $URIMatcher = new URIMatcher();
        $spec = [
            'uri' => '/path/{param}',
            'params' => []
        ];

        $parameterValues = [
            'param' => 1
        ];
        $expectedURIMatchResult = new URIMatchResult(false);
        $URIMatchResult = $URIMatcher->matchAgainstSpec('/path/1', $spec);
        $this->assertEquals($expectedURIMatchResult, $URIMatchResult);
    }
}
