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

use PhpDocMaker\Reflection\Entity\ParameterEntity;
use PhpDocMaker\TestSuite\TestCase;

/**
 * FunctionEntityTest class
 */
class FunctionEntityTest extends TestCase
{
    /**
     * @var \PhpDocMaker\Reflection\Entity\FunctionEntity
     */
    protected $Function;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Function = $this->getFunctionEntityFromTests('a_test_function');
    }

    /**
     * Test for `__toString()` magic method
     * @test
     */
    public function testToString()
    {
        $this->assertSame('a_test_function(string $string = \'my string\')', (string)$this->Function);
    }

    /**
     * Test for `getDeprecatedDescription()` method
     * @test
     */
    public function testGetDeprecatedDescription()
    {
        $this->assertSame('<p>Use instead <code>a_test_function()</code></p>', $this->getFunctionEntityFromTests('old_function')->getDeprecatedDescription());
    }

    /**
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame('<p>A test function.</p>' . PHP_EOL . '<p>This function does nothing in particular.</p>', $this->Function->getDocBlockAsString());
    }

    /**
     * Test for `getName()` method
     * @test
     */
    public function testGetName()
    {
        $this->assertSame('a_test_function', $this->Function->getName());
    }

    /**
     * Test for `getParameters()` method
     * @test
     */
    public function testGetParameters()
    {
        $this->assertContainsOnlyInstancesOf(ParameterEntity::class, $this->Function->getParameters());
    }

    /**
     * Test for `getParametersAsString()` method
     * @test
     */
    public function testGetParametersAsString()
    {
        $this->assertSame('string $string = \'my string\'', $this->Function->getParametersAsString());
        $this->assertSame('', $this->getFunctionEntityFromTests('get_woof')->getParametersAsString());
    }

    /**
     * Test for `getReturnTypeAsString()` method
     * @test
     */
    public function testGetReturnTypeAsString()
    {
        $this->assertSame('string', $this->Function->getReturnTypeAsString());
    }

    /**
     * Test for `getReturnDescription()` method
     * @test
     */
    public function testGetReturnDescription()
    {
        $this->assertSame('<p>The initial string with a <code>PHP_EOL</code></p>', $this->Function->getReturnDescription());
    }

    /**
     * Test for `getSeeTags()` method
     * @test
     */
    public function testGetSeeTags()
    {
        $this->assertSame(['https://en.wikipedia.org/wiki/Dog'], $this->getFunctionEntityFromTests('get_woof')->getSeeTags());
    }

    /**
     * Test for `getThrowsTags()` method
     * @test
     */
    public function testGetThrowsTags()
    {
        $this->assertSame([[
            'type' => 'Exception',
            'description' => 'Exception that will always be thowned',
        ]], $this->getFunctionEntityFromTests('throw_an_exception')->getThrowsTags());
    }

    /**
     * Test for `getVisibility()` method
     * @test
     */
    public function testGetVisibility()
    {
        $this->assertSame('', $this->Function->getVisibility());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->Function->isDeprecated());
        $this->assertTrue($this->getFunctionEntityFromTests('old_function')->isDeprecated());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsStatic()
    {
        $this->assertFalse($this->Function->isStatic());
    }
}
