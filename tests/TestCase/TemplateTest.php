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
namespace ApiMaker\Test;

use ApiMaker\TestSuite\TestCase;
use App\Animals\Cat;
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
     * Test for constant template element
     * @test
     */
    public function testConstantTemplate()
    {
        $constant = $this->getClassEntity(Cat::class)->getConstant('LEGS');
        $result = $this->Twig->render('elements/constant.twig', compact('constant'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'costant.html', $result);
    }

    /**
     * Test for function template element
     * @test
     */
    public function testFunctionTemplate()
    {
        $code = 'function myFunc() {}';
        $function = $this->getFunctionEntityFromString($code);
        $result = $this->Twig->render('elements/method.twig', ['method' => $function]);
        $this->assertStringEqualsFile(EXPECTED_FILES . 'function1.html', $result);

        $code = <<<HEREDOC
/**
 * A custom function
 * @param int \$int1 An integer
 * @param int \$int2 Another integer
 * @return int Result
 * @deprecated Useless function
 * @throws \LogicException if `\$int1` it is less than 1
 * @throws \RuntimeException if `\$int2` it is less than 1
 */
function myFunc(int \$int1 = 2, int \$int2 = 4): int
{
    if (\$int1 < 1) {
        throw new \LogicException('\$int1 must be greater than 1');
    }
    if (\$int2 < 1) {
        throw new \RuntimeException('\$int2 must be greater than 1');
    }
    return \$int1 + \$int2 + 2;
}
HEREDOC;
        $function = $this->getFunctionEntityFromString($code);
        $result = $this->Twig->render('elements/method.twig', ['method' => $function]);
        $this->assertStringEqualsFile(EXPECTED_FILES . 'function2.html', $result);
    }

    /**
     * Test for method template element
     * @test
     */
    public function testMethodTemplate()
    {
        $method = $this->getClassEntity(Cat::class)->getMethod('doMeow');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method1.html', $result);

        $code = <<<HEREDOC
class MyClass {
    /**
     * This a custom method
     * @deprecated This method is deprecated
     * @return string A string as result
     * @throws \RuntimeException
     */
    public function myMethod(): string {}
}
HEREDOC;
        $method = $this->getClassEntityFromString($code)->getMethod('myMethod');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method2.html', $result);

        $code = <<<HEREDOC
class MyClass {
    /**
     * This a custom method
     * @param string \$first First argument
     * @param array \$second Second argument
     * @deprecated This method is deprecated
     * @return string A string as result
     * @see http://example.com/first-link
     * @see http://example.com/second-link
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function myMethod(string \$first, array \$second = []): string {}
}
HEREDOC;
        $method = $this->getClassEntityFromString($code)->getMethod('myMethod');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method3.html', $result);

        $code = <<<HEREDOC
class MyClass {
    public function myMethod(): string {}
}
HEREDOC;
        $method = $this->getClassEntityFromString($code)->getMethod('myMethod');
        $result = $this->Twig->render('elements/method.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method4.html', $result);
    }

    /**
     * Test for method summary template element
     * @test
     */
    public function testMethodSummaryTemplate()
    {
        $method = $this->getClassEntity(Cat::class)->getMethod('doMeow');
        $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary1.html', $result);

        $method = $this->getClassEntity(Cat::class)->getMethod('name');
        $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary2.html', $result);

        $method = $this->getClassEntity(Cat::class)->getMethod('getType');
        $result = $this->Twig->render('elements/method-summary.twig', compact('method'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'method_summary3.html', $result);
    }

    /**
     * Test for property template element
     * @test
     */
    public function testPropertyTemplate()
    {
        $property = $this->getClassEntity(Cat::class)->getProperty('description');
        $result = $this->Twig->render('elements/property.twig', compact('property'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'property1.html', $result);

        $property = $this->getClassEntity(Cat::class)->getProperty('Puppy');
        $result = $this->Twig->render('elements/property.twig', compact('property'));
        $this->assertStringEqualsFile(EXPECTED_FILES . 'property2.html', $result);
    }
}
