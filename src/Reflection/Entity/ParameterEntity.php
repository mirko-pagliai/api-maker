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
namespace PhpDocMaker\Reflection\Entity;

use PhpDocMaker\Reflection\AbstractEntity;
use PhpDocMaker\Reflection\AbstractMethodEntity;
use PhpDocMaker\Reflection\Entity\FunctionEntity;
use PhpDocMaker\Reflection\Entity\MethodEntity;
use PhpDocMaker\Reflection\Entity\Traits\GetTypeAsStringTrait;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionParameter;

/**
 * Parameter entity
 */
class ParameterEntity extends AbstractEntity
{
    use GetTypeAsStringTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionParameter
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionParameter $parameter A `ReflectionParameter` instance
     */
    public function __construct(ReflectionParameter $parameter)
    {
        $this->reflectionObject = $parameter;
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return '$' . $this->getName();
    }

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    public function toSignature(): string
    {
        $signature = (string)$this;

        if ($this->getTypeAsString()) {
            $signature = $this->getTypeAsString() . ' ' . $signature;
        }
        if ($this->reflectionObject->isDefaultValueAvailable()) {
            $signature .= $this->getDefaultValueAsString();
        }

        return $signature;
    }

    /**
     * Gets the default value
     * @return string
     */
    public function getDefaultValueAsString(): string
    {
        if (!($this->reflectionObject->isOptional() && $this->reflectionObject->isDefaultValueAvailable())) {
            return '';
        }

        $defaultValue = $this->reflectionObject->getDefaultValue();

        if (is_array($defaultValue)) {
            return ' = []';
        }
        if (is_null($defaultValue)) {
            return ' = null';
        }
        if (is_string($defaultValue) && strlen($defaultValue) > 15) {
            return ' = ' . var_export(substr($defaultValue, 0, 15) . '...', true);
        }

        return ' = ' . str_replace('\\\\', '\\', var_export($defaultValue, true));
    }

    /**
     * Gets the doc block as string
     * @return string
     */
    public function getDocBlockAsString(): string
    {
        //Takes the right parameter
        $param = array_value_first(array_filter($this->getDeclaringFunction()->getTagsByName('param'), function (Param $param) {
            return $param->getVariableName() === $this->getName();
        }));

        return $param ? $param->getDescription()->render() : '';
    }

    /**
     * Gets the declaring function or method for this parameter
     * @return \PhpDocMaker\Reflection\Entity\FunctionEntity|\PhpDocMaker\Reflection\Entity\MethodEntity
     */
    public function getDeclaringFunction(): AbstractMethodEntity
    {
        $declaringFunction = $this->reflectionObject->getDeclaringFunction();
        $entityClass = $declaringFunction instanceof ReflectionMethod ? MethodEntity::class : FunctionEntity::class;

        return new $entityClass($declaringFunction);
    }
}
