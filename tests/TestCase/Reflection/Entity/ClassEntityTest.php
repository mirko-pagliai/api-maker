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
use App\Vehicles\Car;
use App\Vehicles\Interfaces\Brake;
use App\Vehicles\Interfaces\MotorVehicle;
use PhpDocMaker\Reflection\Entity\ClassEntity;
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

        $this->Class = $this->Class ?? ClassEntity::createFromName(Cat::class);
    }

    /**
     * Test for `getSignature()` method
     * @test
     */
    public function testGetSignature()
    {
        $this->assertSame('App\Animals\Cat', $this->Class->getSignature());
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
        $constants = $this->Class->getConstants();
        $this->assertContainsOnlyInstancesOf(ConstantEntity::class, $constants);
        $this->assertSame(['GENUS', 'LEGS'], $constants->map(function (ConstantEntity $constant) {
            return $constant->getName();
        })->toList());
    }

    /**
     * Test for `getInterfaces()` method
     * @test
     */
    public function testGetInterfaces()
    {
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
        $methods = $this->Class->getMethods();
        $this->assertContainsOnlyInstancesOf(MethodEntity::class, $methods);
        $this->assertSame(['createPuppy', 'setPuppy', 'doMeow', 'setDescription', 'getType'], $methods->map(function (MethodEntity $method) {
            return $method->getName();
        })->toList());
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
        $properties = $this->Class->getProperties();
        $this->assertContainsOnlyInstancesOf(PropertyEntity::class, $properties);
        $this->assertSame(['Puppy', 'description', 'isCat'], $properties->map(function (PropertyEntity $property) {
            return $property->getName();
        })->toList());
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
     * Test for `getTraits()` method
     * @test
     */
    public function testGetTraits()
    {
        $traits = $this->Class->getTraits();
        $this->assertContainsOnlyInstancesOf(ClassEntity::class, $traits);
        $this->assertSame(['ColorsTrait', 'PositionTrait'], $traits->map(function (ClassEntity $trait) {
            return $trait->getShortName();
        })->toList());
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
