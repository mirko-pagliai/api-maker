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
namespace PhpDocMaker\Test;

use App\Animals\Cat;
use App\DeprecatedClassExample;
use Cake\Collection\Collection;
use PhpDocMaker\ErrorCatcher;
use PhpDocMaker\Reflection\Entity\ClassEntity;
use PhpDocMaker\TestSuite\TestCase;

/**
 * ErrorCatcherTest class
 */
class ErrorCatcherTest extends TestCase
{
    /**
     * This method is called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        ErrorCatcher::reset();
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        ErrorCatcher::reset();
    }

    /**
     * Test some errors
     */
    public function testSomeErrors()
    {
        $class = ClassEntity::createFromName(DeprecatedClassExample::class);
        $class->getMethod('methodWithAnotherInvalidTag')->getTags();
        $class->getMethod('methodWithInvalidTag')->getTags();
        $this->assertSame([
            'Invalid tag `@@todo This `todo` is invalid`',
            'Invalid tag `@see 1`',
        ], ErrorCatcher::getAll()->extract('message')->toArray());
    }

    /**
     * Test for `append()` method
     * @test
     */
    public function testAppend()
    {
        $class = ClassEntity::createFromName(Cat::class);
        ErrorCatcher::append($class, 'Error for this class');
        ErrorCatcher::append($class, 'A second error for this class');
        ErrorCatcher::append($class->getMethod('doMeow'), 'Error for a method owned by the class');

        $result = ErrorCatcher::getAll();
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertFalse(ErrorCatcher::getAll()->isEmpty());
        foreach ($result as $error) {
            $this->assertStringStartsWith('App\Animals\Cat', $error['entity']);
            $this->assertNotEmpty($error['message']);
            $this->assertStringEndsWith('Cat.php', $error['filename']);
            $this->assertGreaterThan(0, $error['line']);
        }
    }

    /**
     * Test for `reset()` method
     * @test
     */
    public function testReset()
    {
        ErrorCatcher::append(ClassEntity::createFromName(Cat::class), 'Error for this class');
        $this->assertFalse(ErrorCatcher::getAll()->isEmpty());

        ErrorCatcher::reset();
        $this->assertTrue(ErrorCatcher::getAll()->isEmpty());
    }
}
