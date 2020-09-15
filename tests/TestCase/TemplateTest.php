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
use App\SimpleClassExample;
use App\Vehicles\Car;
use PhpDocMaker\PhpDocMaker;
use PhpDocMaker\Reflection\Entity\ClassEntity;
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

        $this->Class = $this->Class ?? ClassEntity::createFromName(Cat::class);
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
        foreach ([
            $this->Class->getConstant('LEGS'),
            ClassEntity::createFromName(Car::class)->getConstant('TYPES'),
        ] as $k => $costantEntity) {
            $result = $this->Twig->render('elements/constant.twig', ['constant' => $costantEntity]);
            $this->assertStringEqualsFile(EXPECTED_FILES . 'constant' . ++$k . '.html', $result);
        }
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
            $result = trim(preg_replace('/(' . PHP_EOL . '){3,}/', PHP_EOL . PHP_EOL, $result), PHP_EOL);
            $this->assertStringEqualsFile(EXPECTED_FILES . 'function' . ++$k . '.html', $result);

            $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
            $result = trim(preg_replace('/(' . PHP_EOL . '){3,}/', PHP_EOL . PHP_EOL, $result), PHP_EOL);
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
        $class = ClassEntity::createFromName(DeprecatedClassExample::class);
        foreach (['anonymousMethod', 'anotherAnonymousMethod', 'anonymousMethodWithSomeVars', 'anonymousMethodWithoutDocBlock'] as $k => $methodName) {
            $method = $class->getMethod($methodName);
            $result = $this->Twig->render('elements/method.twig', compact('method'));
            $result = trim(preg_replace('/(' . PHP_EOL . '){3,}/', PHP_EOL . PHP_EOL, $result), PHP_EOL);
            $this->assertStringEqualsFile(EXPECTED_FILES . 'method' . ++$k . '.html', $result);
        }

        foreach (['doMeow', 'name', 'getType'] as $k => $methodName) {
            $method = $this->Class->getMethod($methodName);
            $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
            $result = trim(preg_replace('/(' . PHP_EOL . '){3,}/', PHP_EOL . PHP_EOL, $result), PHP_EOL);
            $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary' . ++$k . '.html', $result);
        }
    }

    /**
     * Test for `elements/property.twig` template element
     * @test
     */
    public function testPropertyTemplate()
    {
        foreach (['description', 'Puppy'] as $k => $propertyName) {
            $property = $this->Class->getProperty($propertyName);
            $result = $this->Twig->render('elements/property.twig', compact('property'));
            $this->assertStringEqualsFile(EXPECTED_FILES . 'property' . ++$k . '.html', $result);
        }
    }

    /**
     * Test for `elements/class.twig` template element
     * @test
     */
    public function testClassTemplate()
    {
        foreach ([SimpleClassExample::class, \stdClass::class] as $k => $className) {
            $class = ClassEntity::createFromName($className);
            $result = $this->Twig->render('elements/class.twig', compact('class'));
            $result = trim(preg_replace('/(' . PHP_EOL . '){3,}/', PHP_EOL . PHP_EOL, $result), PHP_EOL);
            $this->assertStringEqualsFile(EXPECTED_FILES . 'class' . ++$k . '.html', $result);
        }
    }
}
