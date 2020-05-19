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
use ApiMaker\Reflection\Entity\ConstantEntity;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\ClassReflector;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;
use RuntimeException;
use Tools\TestSuite\TestCase as BaseTestCase;

/**
 * TestCase class
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Internal method to get a `ReflectionClass` instance
     * @param string $class Class
     * @return \Roave\BetterReflection\Reflection\ReflectionClass
     */
    protected function getReflectionClass(string $class): ReflectionClass
    {
        return (new BetterReflection())
            ->classReflector()
            ->reflect($class);
    }

    /**
     * Internal method to get a `ClassEntity` instance
     * @param string $class Class name
     * @return \ApiMaker\Reflection\Entity\ClassEntity
     */
    protected function getClassEntity(string $class): ClassEntity
    {
        return new ClassEntity($this->getReflectionClass($class));
    }

    /**
     * Internal method to get a `ConstantEntity` instance
     * @param string $constant Constant name
     * @param string $class Class name
     * @return \ApiMaker\Reflection\Entity\ConstantEntity
     */
    protected function getConstantEntity(string $constant, string $class): ConstantEntity
    {
        return new ConstantEntity($this->getReflectionClass($class)->getReflectionConstant($constant));
    }

    /**
     * Internal method to get a `ClassEntity` instance from a string
     * @param string $code Class code
     * @param string|null $className Class name
     * @return \ApiMaker\Reflection\Entity\ClassEntity
     */
    protected function getClassEntityFromString(string $code, ?string $className = null): ClassEntity
    {
        return new ClassEntity($this->getReflectionClassFromString($code, $className));
    }

    /**
     * Internal method to get a `ReflectionClass` instance from a string
     * @param string $code Class code
     * @param string|null $className Class name
     * @return \Roave\BetterReflection\Reflection\ReflectionClass
     * @throws \RuntimeException
     */
    protected function getReflectionClassFromString(string $code, ?string $className = null): ReflectionClass
    {
        if (!$className) {
            if (!preg_match('/^class\s(\S+)/m', $code, $matches)) {
                throw new RuntimeException('Impossible to self-determine the class name');
            }
            $className = $matches[1];
        }

        $code = string_starts_with('<?php', $code) ? $code : '<?php' . PHP_EOL . $code;

        $astLocator = (new BetterReflection())->astLocator();
        $reflector = new ClassReflector(new StringSourceLocator($code, $astLocator));

        return $reflector->reflect($className);
    }
}
