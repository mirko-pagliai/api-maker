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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * PhpDocMakerCommandTest class
 */
class PhpDocMakerCommandTest extends TestCase
{
    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $source = TESTS . DS . 'test_app';

        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = new PhpDocMaker($source);
        $Command->PhpDocMaker->Twig = $this->getTwigMock();
        $Command->PhpDocMaker->Filesystem = $this->getMockBuilder(Filesystem::class)->getMock();
        $commandTester = new CommandTester($Command);

        //Tests options
        $expectedOptions = [
            'debug' => true,
            'target' => TMP . 'output',
            'title' => 'A project title',
        ];
        $commandTester->execute(compact('source') + [
            '--debug' => true,
            '--target' => TMP . 'output',
            '--title' => 'A project title',
        ]);
        debug($commandTester->getDisplay());
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertEquals($expectedOptions, $commandTester->getInput()->getOptions());

        //Tests options from xml file
        $xmlFile = $source . DS . 'php-doc-maker.xml';
        $xml = <<<HEREDOC
<?xml version="1.0" encoding="UTF-8" ?>
<php-doc-maker>
    <title>My test app</title>
    <target>/tmp/php-doc-maker/output</target>
    <debug>true</debug>
</php-doc-maker>
HEREDOC;
        file_put_contents($xmlFile, $xml);
        $commandTester->execute(compact('source'));
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertEquals(['title' => 'My test app'] + $expectedOptions, $commandTester->getInput()->getOptions());
        @unlink($xmlFile);

        //Tests output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Reading sources from: ' . TESTS . DS . 'test_app', $output);
        $this->assertStringContainsString('Target directory: ' . TMP . 'output', $output);
        $this->assertRegExp('/Founded \d+ classes/', $output);
        $this->assertRegExp('/Founded \d+ functions/', $output);
        $this->assertStringContainsString('Rendered index page', $output);
        $this->assertStringContainsString('Rendered functions page', $output);
        $this->assertStringContainsString('Rendered class page for', $output);
        $this->assertRegExp('/Elapsed time\: \d+\.\d+ seconds/', $output);
    }

    /**
     * Test for `execute()` method, on failure
     * @test
     */
    public function testExecuteOnFailure()
    {
        $expectedException = new Exception('Something went wrong...');
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs([TESTS . DS . 'test_app'])
            ->setMethods(['build'])
            ->getMock();

        $Command->PhpDocMaker->method('build')
            ->willThrowException($expectedException);

        putenv('COLUMNS=120');
        $commandTester = new CommandTester($Command);
        $commandTester->execute(['source' => TESTS . DS . 'test_app']);
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[ERROR] Something went wrong...', $output);
        $this->assertStringContainsString(sprintf('On file `%s`', $expectedException->getFile()), $output);
        $this->assertStringContainsString(sprintf('line %s', $expectedException->getLine()), $output);
    }
}
