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
namespace ApiMaker\TestSuite;

use ApiMaker\Reflection\Entity\ClassEntity;
use ApiMaker\Reflection\Entity\FunctionEntity;
use ApiMaker\ReflectorExplorer;
use Roave\BetterReflection\BetterReflection;
use RuntimeException;
use Tools\TestSuite\TestCase as BaseTestCase;

/**
 * TestCase class
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Internal method to get a `ClassEntity` instance
     * @param string $class Class name
     * @return \ApiMaker\Reflection\Entity\ClassEntity
     */
    protected function getClassEntity(string $class): ClassEntity
    {
        $reflection = (new BetterReflection())
            ->classReflector()
            ->reflect($class);

        return new ClassEntity($reflection);
    }

    /**
     * Internal method to get a `ClassEntity` instance from a function located
     *  in the test app
     * @param string $className Class name
     * @return \ApiMaker\Reflection\Entity\ClassEntity
     * @throws \RuntimeException
     */
    protected function getClassEntityFromTests(string $className): ClassEntity
    {
        $reflectorExplorer = new ReflectorExplorer(TEST_APP);

        foreach ($reflectorExplorer->getAllClasses() as $currentClass) {
            if ($currentClass->getName() === $className) {
                return $currentClass;
            }
        }

        throw new RuntimeException(sprintf('Impossible to find the `%s` class from test files', $className));
    }

    /**
     * Internal method to get a `FunctionEntity` instance from a function located
     *  in the test app (see `tests/test_app/functions.php` file)
     * @param string $functionName Function name
     * @return \ApiMaker\Reflection\Entity\FunctionEntity
     * @throws \RuntimeException
     */
    protected function getFunctionEntityFromTests(string $functionName): FunctionEntity
    {
        $reflectorExplorer = new ReflectorExplorer(TEST_APP);

        foreach ($reflectorExplorer->getAllFunctions() as $currentFunction) {
            if ($currentFunction->getName() === $functionName) {
                return $currentFunction;
            }
        }

        throw new RuntimeException(sprintf('Impossible to find the `%s()` function from test files', $functionName));
    }
}
