<?php

namespace Freesewing\Tests;

class CoordsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @param string $attribute Attribute to check for
     *
     * @dataProvider providerTestAttributeExists
     */
    public function testAttributeExists($attribute)
    {
        $this->assertClassHasAttribute($attribute, '\Freesewing\Coords');
    }

    public function providerTestAttributeExists()
    {
        return [
            ['x'],
            ['y'],
        ];
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'get' or 'set'
     * @param $expectedResult Result to check for
     *
     * @dataProvider providerGettersReturnWhatSettersSet
     */
    public function testGettersReturnWhatSettersSet($methodSuffix, $expectedResult)
    {
        $point = new \Freesewing\Coords();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $point->{$setMethod}($expectedResult);
        $this->assertEquals($expectedResult, $point->{$getMethod}());
    }

    public function providerGettersReturnWhatSettersSet()
    {
        return [
            ['X', 52],
            ['Y', 69],
        ];
    }

    /**
     * @param string $methodSuffix The part of the method to call without 'set'
     *
     * @dataProvider providerSetXAndSetYSetNonNumericValuesToZero
     */
    public function testSetXAndSetYSetNonNumericValuesToZero($methodSuffix)
    {
        $point = new \Freesewing\Coords();
        $setMethod = 'set'.$methodSuffix;
        $getMethod = 'get'.$methodSuffix;
        $point->{$setMethod}('sorcha');
        $this->assertEquals(0, $point->{$getMethod}());
    }

    public function providerSetXAndSetYSetNonNumericValuesToZero()
    {
        return [
            ['X'],
            ['Y'],
        ];
    }
}