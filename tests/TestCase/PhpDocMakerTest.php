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

use PhpDocMaker\PhpDocMaker;
use PhpDocMaker\TestSuite\TestCase;
use Tools\TestSuite\EventAssertTrait;
use Twig\Environment;
use Twig\Extension\DebugExtension;

/**
 * PhpDocMakerTest class
 */
class PhpDocMakerTest extends TestCase
{
    use EventAssertTrait;

    /**
     * @var \PhpDocMaker\PhpDocMaker
     */
    protected $PhpDocMaker;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->PhpDocMaker = $this->PhpDocMaker ?? new PhpDocMaker(TESTS . DS . 'test_app', ['debug' => true]);
    }

    /**
     * Test for `__construct()` method
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceof(Environment::class, $this->PhpDocMaker->Twig);
        $this->assertTrue($this->PhpDocMaker->Twig->isDebug());
        $this->assertTrue($this->PhpDocMaker->Twig->isStrictVariables());
        $this->assertNotEmpty($this->PhpDocMaker->Twig->getExtension(DebugExtension::class));
        $this->assertEquals([
            'cache' => true,
            'title' => 'test_app',
            'debug' => true,
        ], $this->PhpDocMaker->getOptions());
    }

    /**
     * Test for `build()` method
     * @test
     */
    public function testBuild()
    {
        $target = TMP . 'output';
        rmdir_recursive($target);
        $cacheFile = TMP . 'output' . DS . 'cache' . DS . 'example';
        create_file($cacheFile);

        $this->PhpDocMaker->Twig = $this->getTwigMock();
        $this->PhpDocMaker->build($target);

        foreach ([
            'classes.founded',
            'functions.founded',
            'index.rendered',
            'functions.rendering',
            'functions.rendered',
            'class.rendering',
            'class.rendered',
        ] as $expectedEvent) {
            $this->assertEventFired($expectedEvent, $this->PhpDocMaker->getEventDispatcher());
        }

        foreach ([
            'assets' . DS . 'bootstrap' . DS . 'bootstrap.min.css',
            'assets' . DS . 'highlight' . DS . 'styles' . DS . 'default.css',
            'assets' . DS . 'highlight' . DS . 'highlight.pack.js',
            'cache',
            'functions.html',
            'index.html',
        ] as $expectedFile) {
            $this->assertFileExists($target . DS . $expectedFile);
        }
        $this->assertFileExists($cacheFile);

        $PhpDocMaker = new PhpDocMaker(TESTS . DS . 'test_app', ['cache' => false]);
        $PhpDocMaker->Twig = $this->getTwigMock();
        $PhpDocMaker->build($target);
        $this->assertFileNotExists($cacheFile);
    }
}
