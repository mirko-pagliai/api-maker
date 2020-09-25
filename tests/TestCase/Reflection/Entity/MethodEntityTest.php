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

use App\Animals\Animal;
use App\Animals\Cat;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\Reflection\Entity\MethodEntity;
use PhpDocMaker\Reflection\Entity\ParameterEntity;
use PhpDocMaker\TestSuite\TestCase;

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
        return ClassEntity::createFromName($class)->getMethod($method);
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('App\Animals\Cat::createPuppy()', (string)$this->getMethodEntity('createPuppy'));
        $this->assertSame('App\Animals\Animal::name()', (string)$this->getMethodEntity('name'));
    }

    /**
     * Test for `toSignature()` method
     * @test
     */
    public function testToSignature()
    {
        $this->assertSame('createPuppy()', $this->getMethodEntity('createPuppy')->toSignature());
        $this->assertSame('name(string|null $name = null)', $this->getMethodEntity('name')->toSignature());
    }

    /**
     * Test for `getDeclaringClass()` method
     * @test
     */
    public function testGetDeclaringClass()
    {
        $class = $this->getMethodEntity('createPuppy')->getDeclaringClass();
        $this->assertInstanceOf(ClassEntity::class, $class);
        $this->assertSame(Cat::class, $class->getName());
    }

    /**
     * Test for `getParameter()` method
     * @test
     */
    public function testGetParameter()
    {
        $parameter = $this->getMethodEntity('doMeow')->getParameter('meow');
        $this->assertInstanceOf(ParameterEntity::class, $parameter);
        $this->assertSame('meow', $parameter->getName());
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
     * Test for `getVisibility()` method
     * @test
     */
    public function testGetVisibility()
    {
        $this->assertSame('public', $this->getMethodEntity('name')->getVisibility());
        $this->assertSame('protected', $this->getMethodEntity('resetPosition')->getVisibility());
    }

    /**
     * Test for `isAbstract()` method
     * @test
     */
    public function testIsAbstract()
    {
        $this->assertTrue($this->getMethodEntity('createPuppy', Animal::class)->isAbstract());
        $this->assertFalse($this->getMethodEntity('createPuppy')->isAbstract());
    }

    /**
     * Test for `isStatic()` method
     * @test
     */
    public function testIsStatic()
    {
        $this->assertTrue($this->getMethodEntity('getType')->isStatic());
        $this->assertFalse($this->getMethodEntity('setDescription')->isStatic());
    }
}
