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
use App\Vehicles\Car;
use App\Vehicles\Interfaces\MotorVehicle;
use InvalidArgumentException;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\TestSuite\TestCase;
use RuntimeException;

/**
 * TagEntityTest class
 */
class TagEntityTest extends TestCase
{
    /**
     * @var \\PhpDocMaker\Reflection\Entity\MethodEntity
     */
    protected $Method;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Method = $this->Method ?: ClassEntity::createFromName(Cat::class)->getMethod('doMeow');
    }

    /**
     * Test for `__construct()` method, with an invalid tags
     * @test
     */
    public function testConstructWithInvalidTags()
    {
        $this->assertException(InvalidArgumentException::class, function () {
            ClassEntity::createFromName(DeprecatedClassExample::class)->getMethod('methodWithAnotherInvalidTag')->getTags();
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid tag `@see \1`');
        ClassEntity::createFromName(DeprecatedClassExample::class)->getMethod('methodWithInvalidTag')->getTagsByName('see');
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        foreach (['param', 'return', 'see', 'throws'] as $tagName) {
            foreach ($this->Method->getTagsByName($tagName) as $tag) {
                $this->assertSame($tagName, (string)$tag);
            }
        }
    }

    /**
     * Test for `getSignature()` method
     * @test
     */
    public function testGetSignature()
    {
        foreach (['param', 'return', 'see', 'throws'] as $tagName) {
            foreach ($this->Method->getTagsByName($tagName) as $tag) {
                $this->assertSame($tagName, $tag->getSignature());
            }
        }
    }

    /**
     * Test for `getDescription()` method
     * @test
     */
    public function testGetDescription()
    {
        foreach ([
            'link' => 'https://en.wikipedia.org/wiki/Meow',
            'param' => 'Type of meow',
            'return' => 'This method returns void',
            'see' => 'OtherClass::otherMethod()',
            'since' => '1.4.0',
            'throws' => 'With a bad "meow"',
        ] as $tagName => $expectedDescription) {
            if (!$this->Method->hasTag($tagName)) {
                $this->fail('The method has no the `@' . $tagName . ' `tag');
            }

            foreach ($this->Method->getTagsByName($tagName) as $tag) {
                $this->assertSame($expectedDescription, $tag->getDescription());
            }
        }

        $method = ClassEntity::createFromName(Car::class)->getMethod('start');
        $this->assertSame(MotorVehicle::class . '::start()', $method->getTagsByName('see')->first()->getDescription());
    }

    /**
     * Test for `getType()` method
     * @test
     */
    public function testGetType()
    {
        foreach ([
            'param' => 'string',
            'return' => 'void',
            'throws' => 'RuntimeException',
        ] as $tagName => $expectedType) {
            foreach ($this->Method->getTagsByName($tagName) as $tag) {
                $this->assertSame($expectedType, $tag->getType());
            }
        }

        //Tag with no type
        foreach ($this->Method->getTagsByName('see') as $tag) {
            $this->assertEmpty($tag->getType());
        }
    }
}
