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
        $this->assertSame('$name', (string)$this->getPropertyEntity('name'));
        $this->assertSame('$position', (string)$this->getPropertyEntity('position'));
    }

    /**
     * Test for `getDeprecatedDescription()` method
     * @test
     */
    public function testGetDeprecatedDescription()
    {
        $this->assertSame('<p>Useless property</p>', $this->getPropertyEntity('isCat')->getDeprecatedDescription());
    }

    /**
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertEmpty($this->getPropertyEntity('isCat')->getDocBlockAsString());
        $this->assertSame('<p>The name</p>', $this->getPropertyEntity('name')->getDocBlockAsString());
        $this->assertSame('<p>Position.</p>' . PHP_EOL . '<p>This counts the number of steps from the initial position.</p>', $this->getPropertyEntity('position')->getDocBlockAsString());
    }

    /**
     * Test for `getName()` method
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('name', $this->getPropertyEntity('name')->getName());
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
