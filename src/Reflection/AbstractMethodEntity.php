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

use Cake\Collection\Collection;
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
     * @var \Cake\Collection\Collection
     */
    private $parameters;

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
    public function getSignature(): string
    {
        return $this->getName() . '(' . $this->getParametersAsString() . ')';
    }

    /**
     * Gets a parameter
     * @param string $name Parameter name
     * @return \PhpDocMaker\Reflection\Entity\ParameterEntity
     */
    public function getParameter(string $name): ParameterEntity
    {
        return $this->getParameters()->firstMatch(compact('name'));
    }

    /**
     * Gets parameters
     * @return \Cake\Collection\Collection A collection of `ParameterEntity`
     */
    public function getParameters(): Collection
    {
        if (!$this->parameters instanceof Collection) {
            $parameters = $this->reflectionObject->getParameters();

            $this->parameters = collection($parameters)->map(function (ReflectionParameter $parameter) {
                return new ParameterEntity($parameter);
            });
        }

        return $this->parameters;
    }

    /**
     * Gets parameters as string, separated by a comma
     * @return string
     */
    public function getParametersAsString(): string
    {
        return implode(', ', $this->getParameters()->extract('signature')->toList());
    }

    /**
     * Gets the visibility (`public`, `protected` or `private`)
     * @return string
     */
    abstract public function getVisibility(): string;

    /**
     * Returns `true` if the method is abstract
     * @return bool
     */
    abstract public function isAbstract(): bool;

    /**
     * Returns `true` if the method is static
     * @return bool
     */
    abstract public function isStatic(): bool;
}
