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
     * @var array
     */
    protected $classesFromTests;

    /**
     * Internal method to get a `ClassesExplorer` instance
     * @return \PhpDocMaker\ClassesExplorer
     */
    protected function getClassesExplorerInstance(): ClassesExplorer
    {
        $this->ClassesExplorer = $this->ClassesExplorer ?: new ClassesExplorer(TEST_APP);

        return $this->ClassesExplorer;
    }

    /**
     * Gets all classes located in the test app
     * @return array
     */
    protected function getAllClassesFromTests(): array
    {
        $this->classesFromTests = $this->classesFromTests ?: $this->getClassesExplorerInstance()->getAllClasses();

        return $this->classesFromTests;
    }

    /**
     * Internal method to get a `FunctionEntity` instance from a function located
     *  in the test app (see `tests/test_app/functions.php` file)
     * @param string $functionName Function name
     * @return \PhpDocMaker\Reflection\Entity\FunctionEntity
     */
    protected function getFunctionEntityFromTests(string $functionName): FunctionEntity
    {
        foreach ($this->getClassesExplorerInstance()->getAllFunctions() as $currentFunction) {
            if ($currentFunction->getName() === $functionName) {
                return $currentFunction;
            }
        }

        $this->fail(sprintf('Impossible to find the `%s()` function from test files', $functionName));
    }

    /**
     * Gets a mock of Twig (`Environment`). It does not render template files
     * @return \Twig\Environment
     */
    protected function getTwigMock(): Environment
    {
        $PhpDocMaker = new PhpDocMaker(TESTS . DS . 'test_app');

        return $this->getMockBuilder(Environment::class)
            ->setConstructorArgs([new FilesystemLoader($PhpDocMaker->getTemplatePath())])
            ->setMethods(['addPath', 'render', 'setCache'])
            ->getMock();
    }
}
