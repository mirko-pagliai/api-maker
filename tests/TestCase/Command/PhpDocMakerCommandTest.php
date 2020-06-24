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
        $Command = new PhpDocMakerCommand();
        $commandTester = new CommandTester($Command);
        $commandTester->execute([
            'sources' => '',
            '--debug' => true,
            '--target' => TMP . 'output',
            '--title' => 'A project title',
        ]);
        $this->assertSame([
            'debug' => true,
            'title' => 'A project title',
            'target' => '/tmp/php-doc-maker/output',
        ], $commandTester->getInput()->getOptions());

        $Command->PhpDocMaker = new PhpDocMaker(TESTS . DS . 'test_app');
        $Command->PhpDocMaker->Twig = $this->getTwigMock();
        $Command->PhpDocMaker->Filesystem = $this->getMockBuilder(Filesystem::class)->getMock();

        $commandTester = new CommandTester($Command);
        $commandTester->execute([
            'sources' => TESTS . DS . 'test_app',
            '--target' => TMP . 'output',
        ]);
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());

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
        $Command = new PhpDocMakerCommand();
        $Command->PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs([TESTS . DS . 'test_app'])
            ->setMethods(['build'])
            ->getMock();

        $Command->PhpDocMaker->method('build')
            ->willThrowException(new Exception('Something went wrong...'));

        $commandTester = new CommandTester($Command);
        $commandTester->execute(['sources' => TESTS . DS . 'test_app']);
        $this->assertSame(Command::FAILURE, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[ERROR] Something went wrong... ', $output);
    }
}
