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
namespace PhpDocMaker\Test\Command;

use Exception;
use PhpDocMaker\Command\PhpDocMakerCommand;
use PhpDocMaker\PhpDocMaker;
use PhpDocMaker\TestSuite\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * PhpDocMakerCommandTest class
 */
class PhpDocMakerCommandTest extends TestCase
{
    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        @unlink(TESTS . DS . 'test_app' . DS . 'php-doc-maker.xml');
    }

    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $source = TESTS . DS . 'test_app';
        $target = TMP . 'output';

        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = new PhpDocMaker($source);
        $Command->PhpDocMaker->Twig = $this->getTwigMock();
        $Command->PhpDocMaker->Filesystem = $this->getMockBuilder(Filesystem::class)
            ->setMethods(['dumpFile', 'mirror'])
            ->getMock();
        $commandTester = new CommandTester($Command);

        //Tests options
        $expectedOptions = [
            'debug' => true,
            'no-cache' => true,
            'target' => $target,
            'title' => 'A project title',
        ];
        $commandTester->execute(compact('source') + [
            '--debug' => true,
            '--target' => $target,
            '--title' => 'A project title',
        ]);
        $this->assertTrue($commandTester->getOutput()->isVerbose());
        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        //Tests options from xml file
        $xml = <<<HEREDOC
<?xml version="1.0" encoding="UTF-8" ?>
<php-doc-maker>
    <title>My test app</title>
    <target>$target</target>
    <verbose>true</verbose>
</php-doc-maker>
HEREDOC;
        file_put_contents($source . DS . 'php-doc-maker.xml', $xml);
        $expectedOptions = ['title' => 'My test app', 'debug' => false, 'no-cache' => false] + $expectedOptions;
        $commandTester->execute(compact('source'));
        $this->assertTrue($commandTester->getOutput()->isVerbose());
        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        $xml = <<<HEREDOC
<?xml version="1.0" encoding="UTF-8" ?>
<php-doc-maker>
    <title>My test app</title>
    <target>$target</target>
    <debug>true</debug>
</php-doc-maker>
HEREDOC;
        file_put_contents($source . DS . 'php-doc-maker.xml', $xml);
        $expectedOptions = ['debug' => true, 'no-cache' => true] + $expectedOptions;
        $commandTester->execute(compact('source'));
        $this->assertTrue($commandTester->getOutput()->isVerbose());
        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        //Tests output
        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Done!', $output);
        $this->assertRegExp('/Founded \d+ classes/', $output);
        $this->assertRegExp('/Founded \d+ functions/', $output);
        $this->assertRegExp('/Elapsed time\: \d+\.\d+ seconds/', $output);

        $this->assertContains('Sources directory: ' . $source, $output);
        $this->assertContains('Target directory: ' . $target, $output);
        $this->assertContains('Rendered index page', $output);
        $this->assertContains('Rendered functions page', $output);
        $this->assertContains('Rendered class page for', $output);
    }

    /**
     * Test for `execute()` method, on missing Composer autoloader
     * @test
     */
    public function testExecuteMissingComposerAutoloader()
    {
        $Command = new PhpDocMakerCommand();
        $commandTester = new CommandTester($Command);
        $commandTester->execute(['--debug' => true, 'source' => TMP]);
        $this->assertSame(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertContains('[ERROR] Missing Composer autoloader...', $output);
        $this->assertContains(sprintf('On file `%s`', ROOT . DS . 'src' . DS . 'ClassesExplorer.php'), $output);
    }

    /**
     * Test for `execute()` method, on error (notice)
     * @test
     */
    public function testExecuteOnError()
    {
        $source = TESTS . DS . 'test_app';
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs(compact('source'))
            ->setMethods(['build'])
            ->getMock();

        $Command->PhpDocMaker->method('build')->will($this->returnCallback(function () {
            trigger_error('A notice error...', E_USER_NOTICE);
        }));

        $commandTester = new CommandTester($Command);
        $commandTester->execute(['--debug' => true] + compact('source'));
        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertContains('[ERROR] A notice error...', $output);
        $this->assertContains(sprintf('On file `%s`', __FILE__), $output);
    }

    /**
     * Test for `execute()` method, on error (notice)
     * @test
     */
    public function testExecuteOnSuppressedError()
    {
        $source = TESTS . DS . 'test_app';
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs(compact('source'))
            ->setMethods(['build'])
            ->getMock();

        $Command->PhpDocMaker->method('build')->will($this->returnCallback(function () {
            @trigger_error('A notice error...', E_USER_NOTICE);
        }));

        $commandTester = new CommandTester($Command);
        $commandTester->execute(['--debug' => true] + compact('source'));
        $this->assertSame(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertContains('[OK] Done!', $output);
    }

    /**
     * Test for `execute()` method, on failure
     * @test
     */
    public function testExecuteOnFailure()
    {
        $source = TESTS . DS . 'test_app';
        $expectedException = new Exception('Something went wrong...');
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs(compact('source'))
            ->setMethods(['build'])
            ->getMock();

        $Command->PhpDocMaker->method('build')->willThrowException($expectedException);

        $commandTester = new CommandTester($Command);
        $commandTester->execute(['--debug' => true] + compact('source'));
        $this->assertSame(1, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertContains('[ERROR] Something went wrong...', $output);
        $this->assertContains(sprintf('On file `%s`', $expectedException->getFile()), $output);
        $this->assertContains(sprintf('line %s', $expectedException->getLine()), $output);
    }
}
