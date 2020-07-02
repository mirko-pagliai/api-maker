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
use PhpDocMaker\TestSuite\TestCase;
use PHPUnit\Runner\Version;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * TemplateTest class
 */
class TemplateTest extends TestCase
{
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

        if (!$this->Twig) {
            $loader = new FilesystemLoader(ROOT . DS . 'templates' . DS . 'default');
            $this->Twig = new Environment($loader, ['autoescape' => false]);
        }
    }

    /**
     * Test for `layout/footer` template element
     * @test
     */
    public function testFooterTemplate()
    {
        $result = $this->Twig->render('layout/footer.twig');
        $this->assertStringStartsWith('<div id="footer">', $result);
    }

    /**
     * Test for `layout/menu` template element
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

        $this->skipIf(version_compare(Version::id(), '8', '<'));
        $this->assertStringContainsString('<a href="Class-App-DeprecatedClassExample.html" title="App\DeprecatedClassExample"><del>App\DeprecatedClassExample</del></a>', $result);
    }

    /**
     * Test for constant template element
     * @test
     */
    public function testConstantTemplate()
    {
        $constant = $this->getClassEntityFromTests(Cat::class)->getConstant('LEGS');
        $result = $this->Twig->render('elements/constant.twig', compact('constant'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'costant.html', $result);
    }

    /**
     * Test for function template element
     * @test
     */
    public function testFunctionTemplate()
    {
        $function = $this->getFunctionEntityFromTests('anonymous_function');
        $result = $this->Twig->render('elements/method.twig', ['method' => $function]);
        $this->assertStringEqualsFile(EXPECTED_FILES . 'function1.html', $result);

        $function = $this->getFunctionEntityFromTests('old_function');
        $result = $this->Twig->render('elements/method.twig', ['method' => $function]);
        $this->assertStringEqualsFile(EXPECTED_FILES . 'function2.html', $result);
    }

    /**
     * Test for function summary template element
     * @test
     */
    public function testFunctionSummaryTemplate()
    {
        $function = $this->getFunctionEntityFromTests('anonymous_function');
        $result = $this->Twig->render('elements/method-summary.twig', ['method' => $function]);
        $this->assertStringEqualsFile(EXPECTED_FILES . 'function_summary1.html', $result);

        $function = $this->getFunctionEntityFromTests('old_function');
        $result = $this->Twig->render('elements/method-summary.twig', ['method' => $function]);
        $this->assertStringEqualsFile(EXPECTED_FILES . 'function_summary2.html', $result);
    }

    /**
     * Test for method template element
     * @test
     */
    public function testMethodTemplate()
    {
        $method = $this->getClassEntityFromTests(DeprecatedClassExample::class)->getMethod('anonymousMethod');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method1.html', $result);

        $method = $this->getClassEntityFromTests(DeprecatedClassExample::class)->getMethod('anotherAnonymousMethod');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method2.html', $result);

        $method = $this->getClassEntityFromTests(DeprecatedClassExample::class)->getMethod('anonymousMethodWithSomeVars');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method3.html', $result);

        $method = $this->getClassEntityFromTests(DeprecatedClassExample::class)->getMethod('anonymousMethodWithoutDocBlock');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method4.html', $result);
    }

    /**
     * Test for method summary template element
     * @test
     */
    public function testMethodSummaryTemplate()
    {
        $method = $this->getClassEntityFromTests(Cat::class)->getMethod('doMeow');
        $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary1.html', $result);

        $method = $this->getClassEntityFromTests(Cat::class)->getMethod('name');
        $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary2.html', $result);

        $method = $this->getClassEntityFromTests(Cat::class)->getMethod('getType');
        $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary3.html', $result);
    }

    /**
     * Test for property template element
     * @test
     */
    public function testPropertyTemplate()
    {
        $property = $this->getClassEntityFromTests(Cat::class)->getProperty('description');
        $result = $this->Twig->render('elements/property.twig', compact('property'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'property1.html', $result);

        $property = $this->getClassEntityFromTests(Cat::class)->getProperty('Puppy');
        $result = $this->Twig->render('elements/property.twig', compact('property'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'property2.html', $result);
    }
}
