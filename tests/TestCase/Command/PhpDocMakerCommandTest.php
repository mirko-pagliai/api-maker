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

/**
 * PhpDocMakerCommandTest class
 */
class PhpDocMakerCommandTest extends TestCase
{
    /**
     * @var string
     */
    protected $source = TEST_APP;

    /**
     * @var string
     */
    protected $target = TMP . 'output' . DS;

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        @unlink(TEST_APP . 'php-doc-maker.xml');
    }

    /**
     * Test for `execute()` method, for options
     * @test
     */
    public function testExecuteOptions()
    {
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs([$this->source, $this->target])
            ->setMethods(['build'])
            ->getMock();
        $commandTester = new CommandTester($Command);

        //With default options (and a passed target)
        $expectedOptions = [
            'debug' => false,
            'no-cache' => false,
            'target' => $this->target,
            'title' => null,
        ];
        $commandTester->execute(['source' => $this->source, '--target' => $this->target]);
        $this->assertFalse($commandTester->getOutput()->isVerbose());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        //With passed options
        $expectedOptions = [
            'debug' => true,
            'no-cache' => true,
            'target' => $this->target,
            'title' => 'A project title',
        ];
        $commandTester->execute([
            'source' => $this->source,
            '--debug' => true,
            '--target' => $this->target,
            '--title' => 'A project title',
        ]);
        $this->assertTrue($commandTester->getOutput()->isVerbose());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        //With options from xml file
        $expectedOptions = [
            'title' => 'My test app',
            'debug' => false,
            'no-cache' => false,
            'target' => $this->target,
        ];
        create_file($this->source . DS . 'php-doc-maker.xml', '<?xml version="1.0" encoding="UTF-8" ?>
<php-doc-maker>
    <title>My test app</title>
    <target>' . $this->target . '</target>
    <verbose>true</verbose>
</php-doc-maker>');
        $commandTester->execute(['source' => $this->source]);
        $this->assertTrue($commandTester->getOutput()->isVerbose());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        //With other options from xml file
        $expectedOptions = [
            'debug' => true,
            'no-cache' => true,
        ] + $expectedOptions;
        create_file($this->source . DS . 'php-doc-maker.xml', '<?xml version="1.0" encoding="UTF-8" ?>
<php-doc-maker>
    <title>My test app</title>
    <target>' . $this->target . '</target>
    <debug>true</debug>
</php-doc-maker>');
        $commandTester->execute(['source' => $this->source]);
        $this->assertTrue($commandTester->getOutput()->isVerbose());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());
    }

    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getPhpDocMakerMock();
        $commandTester = new CommandTester($Command);
        $commandTester->execute(['source' => $this->source, '--target' => $this->target]);

        //Tests output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Done!', $output);
        $this->assertRegExp('/Founded \d+ classes/', $output);
        $this->assertRegExp('/Founded \d+ functions/', $output);
        $this->assertRegExp('/Elapsed time\: \d+\.\d+ seconds/', $output);

        $this->assertStringContainsString('Sources directory: ' . $this->source, $output);
        $this->assertStringContainsString('Target directory: ' . $this->target, $output);
        $this->assertStringContainsString('Rendered index page', $output);
        $this->assertStringContainsString('Rendering functions page', $output);
        $this->assertStringContainsString('Rendered functions page', $output);
        $this->assertStringContainsString('Rendering class page for', $output);
        $this->assertStringContainsString('Rendered class page for', $output);
    }

    /**
     * Test for `execute()` method, on missing Composer autoloader
     * @test
     */
    public function testExecuteMissingComposerAutoloader()
    {
        $commandTester = new CommandTester(new PhpDocMakerCommand());
        $commandTester->execute(['--debug' => true, 'source' => TMP]);
        $this->assertSame(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[ERROR] Missing Composer autoloader on', $output);
    }

    /**
     * Test for `execute()` method, on error
     * @test
     */
    public function testExecuteOnError()
    {
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs([$this->source, $this->target])
            ->setMethods(['build'])
            ->getMock();

        $Command->PhpDocMaker->expects($this->at(0))->method('build')->will($this->returnCallback(function () {
            trigger_error('A notice error...', E_USER_NOTICE);
        }));

        //On exception
        $expectedException = new Exception('Something went wrong...');
        $Command->PhpDocMaker->expects($this->at(1))->method('build')->willThrowException($expectedException);

        //On suppressed error
        $Command->PhpDocMaker->expects($this->at(2))->method('build')->will($this->returnCallback(function () {
            @trigger_error('A notice error...', E_USER_NOTICE);
        }));

        $commandTester = new CommandTester($Command);
        $commandTester->execute(['source' => $this->source, '--debug' => true]);
        $this->assertSame(1, $commandTester->getStatusCode());
        $this->assertStringContainsString('[ERROR] A notice error...', $commandTester->getDisplay());
        $this->assertStringContainsString(sprintf('On file `%s`', __FILE__), $commandTester->getDisplay());

        $commandTester->execute(['source' => $this->source, '--debug' => true]);
        $this->assertSame(1, $commandTester->getStatusCode());
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[ERROR] Something went wrong...', $output);
        $this->assertStringContainsString(sprintf('On file `%s`', $expectedException->getFile()), $output);
        $this->assertStringContainsString(sprintf('line %s', $expectedException->getLine()), $output);

        $commandTester->execute(['source' => $this->source, '--debug' => true]);
        $this->assertSame(0, $commandTester->getStatusCode());
        $this->assertStringContainsString('[OK] Done!', $commandTester->getDisplay());
    }
}
