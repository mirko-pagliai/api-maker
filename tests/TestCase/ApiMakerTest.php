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

use PhpDocMaker\ApiMaker;
use PhpDocMaker\TestSuite\TestCase;
use Tools\TestSuite\EventAssertTrait;
use Twig\Environment;
use Twig\Extension\DebugExtension;

/**
 * ApiMakerTest class
 */
class ApiMakerTest extends TestCase
{
    use EventAssertTrait;

    /**
     * @var \ApiMaker\ApiMaker
     */
    protected $ApiMaker;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->ApiMaker = new ApiMaker(TESTS . DS . 'test_app', ['debug' => true]);
    }

    /**
     * Test for `__construct()` method
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceof(Environment::class, $this->ApiMaker->Twig);
        $this->assertTrue($this->ApiMaker->Twig->isDebug());
        $this->assertTrue($this->ApiMaker->Twig->isStrictVariables());
        $this->assertSame([$this->ApiMaker->getTemplatePath()], $this->ApiMaker->Twig->getLoader()->getPaths());
        $this->assertNotEmpty($this->ApiMaker->Twig->getExtension(DebugExtension::class));
    }

    /**
     * Test for `build()` method
     * @test
     */
    public function testBuild()
    {
        $output = TMP . 'output';
        rmdir_recursive($output);

        $this->ApiMaker->Twig = $this->getTwigMock();

        $dispatcher = $this->ApiMaker->getEventDispatcher();
        $this->ApiMaker->build($output);

        $this->assertEventFired('classes.founded', $dispatcher);
        $this->assertEventFired('functions.founded', $dispatcher);

        $this->assertFileExists($output . DS . 'assets' . DS . 'bootstrap' . DS . 'bootstrap.min.css');
        $this->assertFileExists($output . DS . 'assets' . DS . 'highlight' . DS . 'default.css');
        $this->assertFileExists($output . DS . 'assets' . DS . 'highlight' . DS . 'highlight.pack.js');
        $this->assertFileExists($output . DS . 'layout' . DS . 'menu.html');
        $this->assertFileExists($output . DS . 'functions.html');
        $this->assertFileExists($output . DS . 'index.html');
    }
}
