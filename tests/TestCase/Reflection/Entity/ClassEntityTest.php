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
use App\DeprecatedClassExample;
use App\InvalidClassParent;
use App\Vehicles\Car;
use App\Vehicles\Interfaces\Brake;
use App\Vehicles\Interfaces\MotorVehicle;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\Reflection\Entity\ConstantEntity;
use PhpDocMaker\Reflection\Entity\MethodEntity;
use PhpDocMaker\Reflection\Entity\PropertyEntity;
use PhpDocMaker\TestSuite\TestCase;
use RuntimeException;

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

        $this->Class = $this->Class ?: ClassEntity::createFromName(Cat::class);
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('App\Animals\Cat', (string)$this->Class);
    }

    /**
     * Test for `toSignature()` method
     * @test
     */
    public function testToSignature()
    {
        $this->assertSame('App\Animals\Cat', $this->Class->toSignature());
    }

    /**
     * Test for `getConstant()` method
     * @test
     */
    public function testGetConstant()
    {
        $constant = $this->Class->getConstant('LEGS');
        $this->assertInstanceOf(ConstantEntity::class, $constant);
        $this->assertSame('LEGS', $constant->getName());
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
     * Test for `getInterfaces()` method
     * @test
     */
    public function testGetInterfaces()
    {
        $this->assertTrue($this->Class->getInterfaces()->isEmpty());

        $interfaces = ClassEntity::createFromName(Car::class)->getInterfaces();
        $this->assertContainsOnlyInstancesOf(ClassEntity::class, $interfaces);
        $this->assertSame([Brake::class, MotorVehicle::class], $interfaces->map(function (ClassEntity $interface) {
            return $interface->getName();
        })->toList());
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
     * Test for `getParentClass()` method
     * @test
     */
    public function testGetParentClass()
    {
        $parent = $this->Class->getParentClass();
        $this->assertInstanceOf(ClassEntity::class, $parent);
        $this->assertSame(Animal::class, $parent->getName());

        //Class with no parent class
        $this->assertNull($parent->getParentClass());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Class `App\NoExistingClass` could not be found');
        ClassEntity::createFromName(InvalidClassParent::class)->getParentClass();
    }

    /**
     * Test for `getProperty()` method
     * @test
     */
    public function testGetProperty()
    {
        $property = $this->Class->getProperty('Puppy');
        $this->assertInstanceOf(PropertyEntity::class, $property);
        $this->assertSame('Puppy', $property->getName());
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
     * Test for `getSlug()` method
     * @test
     */
    public function testGetSlug()
    {
        $this->assertSame('App-Animals-Cat', $this->Class->getSlug());
    }

    /**
     * Test for `getType()` method
     * @test
     */
    public function testGetType()
    {
        $this->assertSame('Class', $this->Class->getType());
        $this->assertSame('Trait', ClassEntity::createFromName('\App\Animals\Traits\ColorsTrait')->getType());
        $this->assertSame('Abstract', ClassEntity::createFromName(Animal::class)->getType());
        $this->assertSame('Interface', ClassEntity::createFromName(MotorVehicle::class)->getType());
        $this->assertSame('Final Class', ClassEntity::createFromName('\App\Animals\Horse')->getType());
        $this->assertSame('Deprecated Abstract', ClassEntity::createFromName(DeprecatedClassExample::class)->getType());
    }
}
