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

use BadMethodCallException;
use Tools\Exceptionist;

/**
 * ParentAbstractEntity class
 * @method string getName() Gets the object name
 */
abstract class ParentAbstractEntity
{
    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionClass|\Roave\BetterReflection\Reflection\ReflectionClassConstant|\Roave\BetterReflection\Reflection\ReflectionFunctionAbstract|\Roave\BetterReflection\Reflection\ReflectionParameter|\Roave\BetterReflection\Reflection\ReflectionProperty|\phpDocumentor\Reflection\DocBlock\Tag
     */
    protected $reflectionObject;

    /**
     * `__call()` magic method.
     *
     * It allows access to the methods of the reflected object.
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        Exceptionist::methodExists([$this->reflectionObject, $name], sprintf('Method %s::%s() does not exist', get_class($this->reflectionObject), $name), BadMethodCallException::class);

        return call_user_func_array([$this->reflectionObject, $name], $arguments);
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    abstract public function toSignature(): string;
}
