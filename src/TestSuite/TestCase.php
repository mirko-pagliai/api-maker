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
namespace PhpDocMaker\TestSuite;

use PhpDocMaker\ClassesExplorer;
use PhpDocMaker\PhpDocMaker;
use PhpDocMaker\Reflection\Entity\FunctionEntity;
use Symfony\Component\Filesystem\Filesystem;
use Tools\TestSuite\TestCase as BaseTestCase;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * TestCase class
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @var \PhpDocMaker\ClassesExplorer
     */
    protected $ClassesExplorer;

    /**
     * Asserts that the actual string content is equaled to the expected template
     *  file.
     * @param string $expectedTemplateFilename The expected template filename
     * @param string $actualString The actual string content
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    protected static function assertStringEqualsTemplate(string $expectedTemplateFilename, string $actualString, string $message = ''): void
    {
        $message = $message ?: 'Failed asserting that the actual string content is equaled to the `' . $expectedTemplateFilename . '` expected template file';
        if (!(new Filesystem())->isAbsolutePath($expectedTemplateFilename)) {
            $expectedTemplateFilename = EXPECTED_FILES . $expectedTemplateFilename;
        }
        self::assertFileExists($expectedTemplateFilename);
        $actualString = trim($actualString, PHP_EOL);
        $actualFile = trim(file_get_contents($expectedTemplateFilename), PHP_EOL);
        $actualFile = IS_WIN ? preg_replace('/\S+tests\/test_app\//', TEST_APP, $actualFile) : $actualFile;
        self::assertSame($actualFile, $actualString, $message);
    }

    /**
     * Internal method to get a `ClassesExplorer` instance
     * @param string $path Source path
     * @return \PhpDocMaker\ClassesExplorer
     */
    protected function getClassesExplorerInstance(string $path = TEST_APP): ClassesExplorer
    {
        $this->ClassesExplorer = $this->ClassesExplorer ?? new ClassesExplorer($path);

        return $this->ClassesExplorer;
    }

    /**
     * Internal method to get a `FunctionEntity` instance from a function located
     *  in the test app (see `tests/test_app/functions.php` file)
     * @param string $name Function name
     * @return \PhpDocMaker\Reflection\Entity\FunctionEntity
     */
    protected function getFunctionEntityFromTests(string $name): FunctionEntity
    {
        return $this->getClassesExplorerInstance()->getAllFunctions()->firstMatch(compact('name')) ?: $this->fail('Impossible to find the `' . $name . '()` function from test files');
    }

    /**
     * Gets a mock of `PhpDocMaker`.
     *
     * Includes a mock of Twig (`Environment`), so it does not render template
     *  files.
     * @return \PhpDocMaker\PhpDocMaker
     */
    protected function getPhpDocMakerMock(): PhpDocMaker
    {
        $PhpDocMaker = $this->getMockBuilder(PhpDocMaker::class)
            ->setConstructorArgs([TESTS . DS . 'test_app', ['debug' => true]])
            ->setMethods(['getTwig'])
            ->getMock();

        $PhpDocMaker->method('getTwig')->willReturn($this->getTwigMock());

        return $PhpDocMaker;
    }

    /**
     * Gets a mock of Twig (`Environment`).
     *
     * The `render()` method is a stub, so it does not render template files.
     * @return \Twig\Environment
     */
    protected function getTwigMock(): Environment
    {
        return $this->getMockBuilder(Environment::class)
            ->setConstructorArgs([new FilesystemLoader(PhpDocMaker::getTemplatePath()), [
                'debug' => true,
                'autoescape' => false,
                'strict_variables' => true,
            ]])
            ->setMethods(['render'])
            ->getMock();
    }
}
