<?php
declare(strict_types=1);

/**
 * This file is part of api-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/api-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace ApiMaker\Test\Reflection\Entity;

use ApiMaker\Reflection\Entity\MethodEntity;
use ApiMaker\Reflection\Entity\ParameterEntity;
use ApiMaker\TestSuite\TestCase;
use App\Animals\Cat;

/**
 * MethodEntityTest class
 */
class MethodEntityTest extends TestCase
{
    /**
     * Internal method to get a `MethodEntity` instance
     * @param string $method Method name
     * @param string $class Class name
     * @return MethodEntity
     */
    protected function getMethodEntity(string $method, string $class = Cat::class): MethodEntity
    {
        return new MethodEntity($this->getReflectionClass($class)->getMethod($method));
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('createPuppy()', (string)$this->getMethodEntity('createPuppy'));
        $this->assertSame('name(string|null $name = null)', (string)$this->getMethodEntity('name'));
    }

    /**
     * Test for `getDeprecatedDescription()` method
     * @test
     */
    public function testGetDeprecatedDescription()
    {
        $this->assertSame('Use instead `getName()`/`setName()`', $this->getMethodEntity('name')->getDeprecatedDescription());
    }

    /**
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame('<p>Sets or gets the name of this animal</p>', $this->getMethodEntity('name')->getDocBlockAsString());
        $this->assertSame('<p>Creates a puppy.</p>
<p>This method will return a new <code>Cat</code> instance</p>', $this->getMethodEntity('createPuppy')->getDocBlockAsString());
    }

    /**
     * Test for `getName()` method
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('name', $this->getMethodEntity('name')->getName());
    }

    /**
     * Test for `getParameters()` method
     * @test
     */
    public function testGetParameters()
    {
        $parameters = $this->getMethodEntity('setParents')->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertContainsOnlyInstancesOf(ParameterEntity::class, $parameters);

        $this->assertSame([], $this->getMethodEntity('createPuppy')->getParameters());
    }

    /**
     * Test for `getParametersAsString()` method
     * @test
     */
    public function testGetParametersAsString()
    {
        $this->assertSame('string $mother, string $father', $this->getMethodEntity('setParents')->getParametersAsString());
        $this->assertSame('string|null $name = null', $this->getMethodEntity('name')->getParametersAsString());
        $this->assertSame('', $this->getMethodEntity('createPuppy')->getParametersAsString());
    }

    /**
     * Test for `getReturnTypeAsString()` method
     * @test
     */
    public function testGetReturnTypeAsString()
    {
        $this->assertSame('mixed', $this->getMethodEntity('name')->getReturnTypeAsString());
        $this->assertSame('\App\Animals\Cat', $this->getMethodEntity('createPuppy')->getReturnTypeAsString());
        $this->assertSame('string|null', $this->getMethodEntity('getColor')->getReturnTypeAsString());
        $this->assertSame('void', $this->getMethodEntity('doMeow')->getReturnTypeAsString());
    }

    /**
     * Test for `getReturnDescription()` method
     * @test
     */
    public function testGetReturnDescription()
    {
        $this->assertSame('Returns the current instance or the name as string', $this->getMethodEntity('name')->getReturnDescription());
        $this->assertSame('', $this->getMethodEntity('getColor')->getReturnDescription());
        $this->assertSame('', $this->getMethodEntity('doMeow')->getReturnDescription());
    }

    /**
     * Test for `getSeeTags()` method
     * @test
     */
    public function testGetSeeTags()
    {
        $this->assertSame(['https://en.wikipedia.org/wiki/Meow'], $this->getMethodEntity('doMeow')->getSeeTags());
    }

    /**
     * Test for `getThrowsTags()` method
     * @test
     */
    public function testGetThrowsTags()
    {
        $this->assertSame([[
            'type' => '\LogicException',
            'description' => 'If the `LEGS` constant is not defined',
        ]], $this->getMethodEntity('getLegs')->getThrowsTags());
        $this->assertSame([[
            'type' => '\RuntimeException',
            'description' => '',
        ]], $this->getMethodEntity('doMeow')->getThrowsTags());
    }

    /**
     * Test for `getVisibility()` method
     * @test
     */
    public function testGetVisibility()
    {
        $this->assertSame('public', $this->getMethodEntity('name')->getVisibility());
        $this->assertSame('protected', $this->getMethodEntity('resetPosition')->getVisibility());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->getMethodEntity('setName')->isDeprecated());
        $this->assertTrue($this->getMethodEntity('name')->isDeprecated());
    }
}
