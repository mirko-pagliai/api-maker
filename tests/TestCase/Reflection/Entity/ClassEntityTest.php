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

use ApiMaker\Reflection\Entity\ConstantEntity;
use ApiMaker\Reflection\Entity\MethodEntity;
use ApiMaker\Reflection\Entity\PropertyEntity;
use ApiMaker\TestSuite\TestCase;
use App\Animals\Cat;

/**
 * ClassEntityTest class
 */
class ClassEntityTest extends TestCase
{
    /**
     * @var \ApiMaker\Reflection\Entity\ClassEntity
     */
    protected $Class;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Class = $this->getClassEntity(Cat::class);
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
        $class = <<<HEREDOC
/**
 * Deprecated Animal
 * @deprecated use instead the `AnimalClass`
 */
class DeprecatedAnimal { }
HEREDOC;
        $this->assertSame('Use instead the `AnimalClass`', $this->getClassEntityFromString($class)->getDeprecatedDescription());
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
     * Test for `getMethods()` method
     * @test
     */
    public function testGetMethods()
    {
        $this->assertContainsOnlyInstancesOf(MethodEntity::class, $this->Class->getMethods());
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
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->Class->isDeprecated());

        $class = <<<HEREDOC
/**
 * Deprecated Animal
 * @deprecated use instead the `AnimalClass`
 */
class DeprecatedAnimal { }
HEREDOC;
        $this->assertTrue($this->getClassEntityFromString($class)->isDeprecated());
    }
}
