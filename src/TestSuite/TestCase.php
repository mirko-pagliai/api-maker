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
use Tools\TestSuite\TestCase as BaseTestCase;

/**
 * TestCase class
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @var \ApiMaker\ReflectorExplorer
     */
    protected $ReflectorExplorer;

    /**
     * Internal method to get a `ReflectorExplorer` instance
     * @return ReflectorExplorer
     */
    protected function getReflectorExplorerInstance(): ReflectorExplorer
    {
        if (!$this->ReflectorExplorer) {
            $this->ReflectorExplorer = new ReflectorExplorer(TEST_APP);
        }

        return $this->ReflectorExplorer;
    }

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
     */
    protected function getClassEntityFromTests(string $className): ClassEntity
    {
        foreach ($this->getReflectorExplorerInstance()->getAllClasses() as $currentClass) {
            if ($currentClass->getName() === $className) {
                return $currentClass;
            }
        }

        $this->fail(sprintf('Impossible to find the `%s` class from test files', $className));
    }

    /**
     * Internal method to get a `FunctionEntity` instance from a function located
     *  in the test app (see `tests/test_app/functions.php` file)
     * @param string $functionName Function name
     * @return \ApiMaker\Reflection\Entity\FunctionEntity
     */
    protected function getFunctionEntityFromTests(string $functionName): FunctionEntity
    {
        foreach ($this->getReflectorExplorerInstance()->getAllFunctions() as $currentFunction) {
            if ($currentFunction->getName() === $functionName) {
                return $currentFunction;
            }
        }

        $this->fail(sprintf('Impossible to find the `%s()` function from test files', $functionName));
    }
}
