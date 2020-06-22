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

use ApiMaker\Reflection\Entity\ParameterEntity;
use ApiMaker\TestSuite\TestCase;
use App\Animals\Cat;
use App\ClassExample;

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
        return $this->getClassEntity($class)->getMethod($method)->getParameter($parameter);
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('string $name', (string)$this->getParameterEntity('name', 'setName'));
        $this->assertSame('string|array $colors = []', (string)$this->getParameterEntity('colors', 'setColors'));
        $this->assertSame('string|null $name = null', (string)$this->getParameterEntity('name', 'name'));
        $this->assertSame('App\Animals\Cat $puppy', (string)$this->getParameterEntity('puppy', 'setPuppy'));
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
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame(
            '<p>The name or <code>null</code> to get the current name</p>',
            $this->getParameterEntity('name', 'name')->getDocBlockAsString()
        );

        //This parameter has no DocBlock
        $this->assertSame('', $this->getClassEntityFromTests(ClassExample::class)->getMethod('anonymousMethodWithParameterAndWithoutDocBlock')->getParameter('parameterWithoutDocBlock')->getDocBlockAsString());
    }

    /**
     * Test for `getName()` method
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('name', $this->getParameterEntity('name', 'setName')->getName());
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
