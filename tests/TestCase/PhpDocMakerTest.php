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
     * @var string
     */
    protected $target = TMP . 'output' . DS;

    /**
     * Called before each test
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        if (!$this->PhpDocMaker) {
            $this->PhpDocMaker = new PhpDocMaker(TESTS . DS . 'test_app', ['debug' => true]);
            $this->PhpDocMaker->Twig = $this->getTwigMock();
        }
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unlink_recursive($this->target);
    }

    /**
     * Test for `setOption()` method
     * @test
     */
    public function testSetOption()
    {
        $this->assertEquals([
            'cache' => true,
            'title' => 'test_app',
            'debug' => true,
        ], $this->PhpDocMaker->getOptions());

        $result = $this->PhpDocMaker->setOption('cache', false);
        $this->assertInstanceOf(PhpDocMaker::class, $result);
        $this->assertEquals([
            'cache' => false,
            'title' => 'test_app',
            'debug' => true,
        ], $this->PhpDocMaker->getOptions());

        $this->PhpDocMaker->setOption(['title' => 'a new title', 'debug' => false]);
        $this->assertEquals([
            'cache' => false,
            'title' => 'a new title',
            'debug' => false,
        ], $this->PhpDocMaker->getOptions());
    }

    /**
     * Test for `build()` method
     * @test
     */
    public function testBuild()
    {
        $cacheFile = $this->target . 'cache' . DS . 'example';
        create_file($cacheFile);

        $this->PhpDocMaker->build($this->target);

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
            $this->assertFileExists($this->target . $expectedFile);
        }
        $this->assertFileExists($cacheFile);

        //In this case the cache will not be used and will be emptied
        $this->PhpDocMaker->setOption('cache', false)->build($this->target);
        $this->assertFileNotExists($cacheFile);
    }
}
