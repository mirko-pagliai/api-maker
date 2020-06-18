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
namespace ApiMaker\Test\Command;

use ApiMaker\Command\ApiMakerCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Tools\TestSuite\TestCase;

/**
 * ApiMakerCommandTest class
 */
class ApiMakerCommandTest extends TestCase
{
    /**
     * Test for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $commandTester = new CommandTester(new ApiMakerCommand());
        $commandTester->execute([
            'sources' => TESTS . DS . 'test_app',
            '--target' => TMP . 'output',
        ]);
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
}
