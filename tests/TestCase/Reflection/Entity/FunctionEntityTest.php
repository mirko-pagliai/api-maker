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

use ApiMaker\Reflection\Entity\FunctionEntity;
use ApiMaker\Reflection\Entity\ParameterEntity;
use ApiMaker\TestSuite\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionFunction;
use Roave\BetterReflection\Reflector\FunctionReflector;
use Roave\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use RuntimeException;

/**
 * FunctionEntityTest class
 */
class FunctionEntityTest extends TestCase
{
    /**
     * @var \ApiMaker\Reflection\Entity\FunctionEntity
     */
    protected $Function;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Function = $this->getFunctionEntity('a_test_function');
    }

    /**
     * Internal method to get a `FunctionEntity` instance.
     *
     * It looks for the function in the `tests/test_app/functions.php` file.
     * @param string $function Function name
     * @return FunctionEntity
     * @throws RuntimeException
     */
    protected function getFunctionEntity(string $function): FunctionEntity
    {
        $configuration = new BetterReflection();
        $astLocator = $configuration->astLocator();
        $classReflector = $configuration->classReflector();
        $directoriesSourceLocator = new SingleFileSourceLocator(TESTS . DS . 'test_app' . DS . 'functions.php', $astLocator);
        $reflector = new FunctionReflector($directoriesSourceLocator, $classReflector);

        $functions = array_filter($reflector->getAllFunctions(), function (ReflectionFunction $currentFunction) use ($function) {
            return $currentFunction->getName() === $function;
        });

        if (!$functions) {
            throw new RuntimeException(sprintf('Can\'t found `%s()` function', $function));
        }

        return new FunctionEntity(array_value_first($functions));
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
        $this->assertSame('<p>Use instead <code>a_test_function()</code></p>', $this->getFunctionEntity('old_function')->getDeprecatedDescription());
    }

    /**
     * Test for `getDocBlockAsString()` method
     * @test
     */
    public function testGetDocBlockAsString()
    {
        $this->assertSame('<p>A test function.</p>
<p>This function does nothing in particular.</p>', $this->Function->getDocBlockAsString());
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
        $this->assertSame('', $this->getFunctionEntity('get_woof')->getParametersAsString());
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
        $this->assertSame(['https://en.wikipedia.org/wiki/Dog'], $this->getFunctionEntity('get_woof')->getSeeTags());
    }

    /**
     * Test for `getThrowsTags()` method
     * @test
     */
    public function testGetThrowsTags()
    {
        $this->assertSame([[
            'type' => '\Exception',
            'description' => 'Exception that will always be thowned',
        ]], $this->getFunctionEntity('throw_an_exception')->getThrowsTags());
    }

    /**
     * Test for `isDeprecated()` method
     * @test
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->Function->isDeprecated());
        $this->assertTrue($this->getFunctionEntity('old_function')->isDeprecated());
    }
}
