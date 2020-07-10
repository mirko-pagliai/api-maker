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
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\Reflection\Entity\PropertyEntity;
use PhpDocMaker\TestSuite\TestCase;

/**
 * PropertyEntityTest class
 */
class PropertyEntityTest extends TestCase
{
    /**
     * Internal method to get a `PropertyEntity` instance
     * @param string $property Property name
     * @param string $class Class name
     * @return PropertyEntity
     */
    protected function getPropertyEntity(string $property, string $class = Cat::class): PropertyEntity
    {
        return $this->getClassEntityFromTests($class)->getProperty($property);
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('App\Animals\Animal::$name', (string)$this->getPropertyEntity('name'));
        $this->assertSame('App\Animals\Traits\PositionTrait::$position', (string)$this->getPropertyEntity('position'));
    }

    /**
     * Test for `toSignature()` magic method
     * @test
     */
    public function testToSignature()
    {
        $this->assertSame('$name', $this->getPropertyEntity('name')->toSignature());
        $this->assertSame('$position', $this->getPropertyEntity('position')->toSignature());
    }

    /**
     * Test for `getDeclaringClass()` method
     * @test
     */
    public function testGetDeclaringClass()
    {
        $class = $this->getPropertyEntity('isCat')->getDeclaringClass();
        $this->assertInstanceOf(ClassEntity::class, $class);
        $this->assertSame(Cat::class, $class->getName());
    }

    /**
     * Test for `getDeprecatedDescription()` method
     * @test
     */
    public function testGetDeprecatedDescription()
    {
        $this->assertSame('Useless property', $this->getPropertyEntity('isCat')->getDeprecatedDescription());
    }

    /**
     * Test for `getSeeTags()` method
     * @test
     */
    public function testGetSeeTags()
    {
        $this->assertSame([], $this->getPropertyEntity('name')->getSeeTags());
        $this->assertSame(['run()', 'walk()'], $this->getPropertyEntity('position')->getSeeTags());
    }

    /**
     * Test for `getTypeAsString()` method
     * @test
     */
    public function testGetTypeAsString()
    {
        $this->assertSame('bool', $this->getPropertyEntity('isCat')->getTypeAsString());
        $this->assertSame('int', $this->getPropertyEntity('position')->getTypeAsString());
        $this->assertSame('App\Animals\Cat|null', $this->getPropertyEntity('Puppy')->getTypeAsString());
    }

    /**
     * Test for `getVisibility()` method
     * @test
     */
    public function testGetVisibility()
    {
        $this->assertSame('protected', $this->getPropertyEntity('name')->getVisibility());
        $this->assertSame('public', $this->getPropertyEntity('isCat')->getVisibility());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->getPropertyEntity('name')->isDeprecated());
        $this->assertTrue($this->getPropertyEntity('isCat')->isDeprecated());
    }
}
