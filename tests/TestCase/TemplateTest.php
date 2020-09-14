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
use App\Vehicles\Car;
use PhpDocMaker\PhpDocMaker;
use PhpDocMaker\TestSuite\TestCase;

/**
 * TemplateTest class
 */
class TemplateTest extends TestCase
{
    /**
     * @var \PhpDocMaker\Reflection\Entity\ClassEntity
     */
    protected $Class;

    /**
     * @var \Twig\Environment
     */
    protected $Twig;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Class = $this->Class ?? $this->getClassEntityFromTests(Cat::class);
        $this->Twig = $this->Twig ?? PhpDocMaker::getTwig(true);
    }

    /**
     * Test for `layout/footer.twig` layout element
     * @test
     */
    public function testFooterTemplate()
    {
        $result = $this->Twig->render('layout/footer.twig');
        $this->assertStringStartsWith('<div id="footer">', $result);
    }

    /**
     * Test for `layout/menu.twig` layout element
     * @test
     */
    public function testMenuTemplate()
    {
        $expectedStart = <<<HEREDOC
<ul class="list-unstyled m-0">
    <li class="text-truncate">
        <a href="Class-App-Animals-Animal.html" title="App\Animals\Animal">App\Animals\Animal</a>
    </li>
HEREDOC;
        $expectedEnd = <<<HEREDOC
        <a href="Class-Cake-Routing-Router.html" title="Cake\Routing\Router">Cake\Routing\Router</a>
    </li>
</ul>
HEREDOC;
        $classes = $this->getClassesExplorerInstance()->getAllClasses();
        $result = $this->Twig->render('layout/menu.twig', compact('classes'));
        $this->assertStringStartsWith($expectedStart, $result);
        $this->assertStringEndsWith($expectedEnd, $result);
        $this->assertStringContainsString('<a href="Class-App-DeprecatedClassExample.html" title="App\DeprecatedClassExample"><del>App\DeprecatedClassExample</del></a>', $result);
    }

    /**
     * Test for `elements/constant.twig` template element
     * @test
     */
    public function testConstantTemplate()
    {
        $constant = $this->Class->getConstant('LEGS');
        $result = $this->Twig->render('elements/constant.twig', compact('constant'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'constant1.html', $result);

        $constant = $this->getClassEntityFromTests(Car::class)->getConstant('TYPES');
        $result = $this->Twig->render('elements/constant.twig', compact('constant'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'constant2.html', $result);
    }

    /**
     * Test for `elements/method.twig` and `elements/method-summary.twig`
     *  template elements (with functions)
     * @test
     */
    public function testFunctionTemplate()
    {
        foreach (['anonymous_function', 'old_function'] as $k => $functionName) {
            $method = $this->getFunctionEntityFromTests($functionName);

            $result = $this->Twig->render('elements/method.twig', compact('method'));
            $this->assertStringEqualsFile(EXPECTED_FILES . 'function' . ++$k . '.html', $result);

            $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
            $this->assertStringEqualsFile(EXPECTED_FILES . 'function_summary' . $k . '.html', $result);
        }
    }

    /**
     * Test for `elements/method.twig` and `elements/method-summary.twig`
     *  template elements (with methods)
     * @test
     */
    public function testMethodTemplate()
    {
        $class = $this->getClassEntityFromTests(DeprecatedClassExample::class);

        foreach (['anonymousMethod', 'anotherAnonymousMethod', 'anonymousMethodWithSomeVars', 'anonymousMethodWithoutDocBlock'] as $k => $methodName) {
            $method = $class->getMethod($methodName);
            $result = $this->Twig->render('elements/method.twig', compact('method'));
            $this->assertStringEqualsFile(EXPECTED_FILES . 'method' . ++$k . '.html', $result);
        }

        foreach (['doMeow', 'name', 'getType'] as $k => $methodName) {
            $method = $this->Class->getMethod($methodName);
            $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
            $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary' . ++$k . '.html', $result);
        }
    }

    /**
     * Test for `elements/property.twig` template element
     * @test
     */
    public function testPropertyTemplate()
    {
        $property = $this->Class->getProperty('description');
        $result = $this->Twig->render('elements/property.twig', compact('property'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'property1.html', $result);

        $property = $this->Class->getProperty('Puppy');
        $result = $this->Twig->render('elements/property.twig', compact('property'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'property2.html', $result);
    }
}
