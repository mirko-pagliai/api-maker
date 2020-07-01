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
use App\DeprecatedClassExample;
use PhpDocMaker\Reflection\Entity\ConstantEntity;
use PhpDocMaker\Reflection\Entity\MethodEntity;
use PhpDocMaker\Reflection\Entity\PropertyEntity;
use PhpDocMaker\TestSuite\TestCase;

/**
 * ClassEntityTest class
 */
class ClassEntityTest extends TestCase
{
    /**
     * @var \PhpDocMaker\Reflection\Entity\ClassEntity
     */
    protected $Class;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Class = $this->getClassEntityFromTests(Cat::class);
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame(Cat::class, (string)$this->Class);
    }

    /**
     * Test for `getConstant()` method
     * @test
     */
    public function testGetConstant()
    {
        $this->assertInstanceOf(ConstantEntity::class, $this->Class->getConstant('LEGS'));
    }

    /**
     * Test for `getConstants()` method
     * @test
     */
    public function testGetConstants()
    {
        $this->assertContainsOnlyInstancesOf(ConstantEntity::class, $this->Class->getConstants());
    }

    /**
     * Test for `getDeprecatedDescription()` method
     * @test
     */
    public function testGetDeprecatedDescription()
    {
        $this->assertSame('<p>Useless, just for tests</p>', $this->getClassEntityFromTests(DeprecatedClassExample::class)->getDeprecatedDescription());
    }

    /**
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame('<p>Cat class</p>', $this->Class->getDocBlockAsString());
    }

    /**
     * Test for `getLink()` method
     * @test
     */
    public function testGetLink()
    {
        $this->assertSame('Class-App-Animals-Cat.html', $this->Class->getLink());
    }

    /**
     * Test for `getMethod()` method
     * @test
     */
    public function testGetMethod()
    {
        $method = $this->Class->getMethod('createPuppy');
        $this->assertInstanceOf(MethodEntity::class, $method);
        $this->assertSame('createPuppy', $method->getName());
    }

    /**
     * Test for `getMethods()` method
     * @test
     */
    public function testGetMethods()
    {
        $this->assertContainsOnlyInstancesOf(MethodEntity::class, $this->Class->getMethods());
    }

    /**
     * Test for `getName()` method
     * @test
     */
    public function testGetName()
    {
        $this->assertSame(Cat::class, $this->Class->getName());
    }

    /**
     * Test for `getProperty()` method
     * @test
     */
    public function testGetProperty()
    {
        $this->assertInstanceOf(PropertyEntity::class, $this->Class->getProperty('Puppy'));
    }

    /**
     * Test for `getProperties()` method
     * @test
     */
    public function testGetProperties()
    {
        $this->assertContainsOnlyInstancesOf(PropertyEntity::class, $this->Class->getProperties());
    }

    /**
     * Test for `getSeeTags()` method
     * @test
     */
    public function testGetSeeTags()
    {
        $this->assertSame(['https://en.wikipedia.org/wiki/Cat'], $this->Class->getSeeTags());
    }

    /**
     * Test for `getSlug()` method
     * @test
     */
    public function testGetSlug()
    {
        $this->assertSame('App-Animals-Cat', $this->Class->getSlug());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->Class->isDeprecated());
        $this->assertTrue($this->getClassEntityFromTests(DeprecatedClassExample::class)->isDeprecated());
    }
}
