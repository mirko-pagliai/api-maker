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
namespace PhpDocMaker\Reflection;

use PhpDocMaker\Reflection\AbstractEntity;
use PhpDocMaker\Reflection\Entity\ParameterEntity;
use Roave\BetterReflection\Reflection\ReflectionParameter;

/**
 * AbstractMethodEntity class.
 *
 * This class contains methods common to class methods and functions.
 */
abstract class AbstractMethodEntity extends AbstractEntity
{
    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName() . '()';
    }

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    public function toSignature(): string
    {
        return $this->getName() . '(' . $this->getParametersAsString() . ')';
    }

    /**
     * Gets a parameter
     * @param string $parameterName Parameter name
     * @return \PhpDocMaker\Reflection\Entity\ParameterEntity
     */
    public function getParameter(string $parameterName): ParameterEntity
    {
        return new ParameterEntity($this->reflectionObject->getParameter($parameterName));
    }

    /**
     * Gets parameters
     * @return array Array of `ParameterEntity` instances
     */
    public function getParameters(): array
    {
        return array_map(function (ReflectionParameter $parameter) {
            return new ParameterEntity($parameter);
        }, $this->reflectionObject->getParameters());
    }

    /**
     * Gets parameters as string, separated by a comma
     * @return string
     */
    public function getParametersAsString(): string
    {
        return implode(', ', array_map(function (ParameterEntity $param) {
            return $param->toSignature();
        }, $this->getParameters()));
    }

    /**
     * Gets the visibility (`public`, `protected` or `private`)
     * @return string
     */
    abstract public function getVisibility(): string;
}
