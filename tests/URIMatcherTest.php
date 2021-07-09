<?php

require_once(dirname(__FILE__) . '/../src/URIMatcher.class.php');
require_once(dirname(__FILE__) . '/../src/URIMatchResult.class.php');

use PHPUnit\Framework\TestCase;

class URIMatcherTest extends TestCase
{
    /**
     * @test
     */
    public function matchAgainstSpecReturnsTrue()
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
    public function matchAgainstSpecReturnsFalse()
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
    public function matchAgainstSpecReturnsTrueForSpecWithNaturalNumberParameter()
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
    public function matchAgainstSpecReturnsTrueForSpecWithAlphabeticParameter()
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
}
