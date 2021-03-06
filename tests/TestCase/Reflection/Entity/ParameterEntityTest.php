<?php
declare(strict_types=1);

/**
 * This file is part of php-doc-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-doc-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace PhpDocMaker\Test\Reflection\Entity;

use App\Animals\Cat;
use App\ArrayExample;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\Reflection\Entity\FunctionEntity;
use PhpDocMaker\Reflection\Entity\MethodEntity;
use PhpDocMaker\Reflection\Entity\ParameterEntity;
use PhpDocMaker\TestSuite\TestCase;

/**
 * ParameterEntityTest class
 */
class ParameterEntityTest extends TestCase
{
    /**
     * Internal method to get a `ParameterEntity` instance
     * @param string $parameter Parameter name
     * @param string $method Method name
     * @param string $class Class name
     * @return ParameterEntity
     */
    protected function getParameterEntity(string $parameter, string $method, string $class = Cat::class): ParameterEntity
    {
        return ClassEntity::createFromName($class)->getMethod($method)->getParameter($parameter);
    }

    /**
     * Test for `getSignature()` method
     * @test
     */
    public function testGetSignature()
    {
        $this->assertSame('string|null $name = null', $this->getParameterEntity('name', 'name')->getSignature());
        $this->assertSame('string $name', $this->getParameterEntity('name', 'setName')->getSignature());
        $this->assertSame('string|array $colors = []', $this->getParameterEntity('colors', 'setColors')->getSignature());
        $this->assertSame('App\Animals\Cat $puppy', $this->getParameterEntity('puppy', 'setPuppy')->getSignature());
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('$name', (string)$this->getParameterEntity('name', 'setName'));
        $this->assertSame('$colors', (string)$this->getParameterEntity('colors', 'setColors'));
        $this->assertSame('$name', (string)$this->getParameterEntity('name', 'name'));
        $this->assertSame('$puppy', (string)$this->getParameterEntity('puppy', 'setPuppy'));
    }

    /**
     * Test for `getDefaultValueAsString()` method
     * @test
     */
    public function testGetDefaultValueAsString()
    {
        $this->assertSame(' = null', $this->getParameterEntity('name', 'name')->getDefaultValueAsString());
        $this->assertSame('', $this->getParameterEntity('name', 'setName')->getDefaultValueAsString());
        $this->assertSame(' = \'meow!\'', $this->getParameterEntity('meow', 'doMeow')->getDefaultValueAsString());
        $this->assertSame(' = []', $this->getParameterEntity('colors', 'setColors')->getDefaultValueAsString());
        $this->assertSame(' = \'description of ...\'', $this->getParameterEntity('description', 'setDescription')->getDefaultValueAsString());
    }

    /**
     * Test for `getDeclaringFunction()`
     * @test
     */
    public function testGetDeclaringFunction()
    {
        $function = $this->getParameterEntity('name', 'setName')->getDeclaringFunction();
        $this->assertInstanceOf(MethodEntity::class, $function);
        $this->assertSame('setName', $function->getName());

        //Parameter from a function
        $function = $this->getFunctionEntityFromTests('old_function')->getParameter('int2')->getDeclaringFunction();
        $this->assertInstanceOf(FunctionEntity::class, $function);
        $this->assertSame('old_function', $function->getName());
    }

    /**
     * Test for `getDocBlockAsString()`
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame('Type of meow', $this->getParameterEntity('meow', 'doMeow')->getDocBlockAsString());

        //Parameter from a function
        $this->assertSame('Another integer', $this->getFunctionEntityFromTests('old_function')->getParameter('int2')->getDocBlockAsString());

        //Parameter with no DocBlock
        $this->assertSame('', $this->getParameterEntity('offset', 'offsetExists', ArrayExample::class)->getDocBlockAsString());
    }

    /**
     * Test for `getTypeAsString()`
     * @test
     */
    public function testGetTypeAsString()
    {
        $this->assertSame('string|null', $this->getParameterEntity('name', 'name')->getTypeAsString());
        $this->assertSame('string|array', $this->getParameterEntity('colors', 'setColors')->getTypeAsString());
        $this->assertSame('string', $this->getParameterEntity('description', 'setDescription')->getTypeAsString());
        $this->assertSame('App\Animals\Cat', $this->getParameterEntity('puppy', 'setPuppy')->getTypeAsString());
    }
}
