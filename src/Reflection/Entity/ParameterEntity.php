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
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Roave\BetterReflection\Reflection\ReflectionParameter;

/**
 * Parameter entity
 */
class ParameterEntity extends AbstractEntity
{
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
        $string = '$' . $this->reflectionObject->getName();

        if ($this->getTypeAsString()) {
            $string = $this->getTypeAsString() . ' ' . $string;
        }
        if ($this->reflectionObject->isDefaultValueAvailable()) {
            $string .= $this->getDefaultValueAsString();
        }

        return $string;
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
        $docblock = $this->getDocBlockInstance($this->reflectionObject->getDeclaringFunction());
        if (!$docblock) {
            return '';
        }

        //Takes the right parameter
        $param = array_value_first(array_filter($docblock->getTagsByName('param'), function (Param $param) {
            return $param->getVariableName() === $this->reflectionObject->getName();
        }));
        $docComment = $param->getDescription()->render();

        return $docComment ? $this->toHtml($docComment) : '';
    }

    /**
     * Gets the type
     * @return string
     */
    public function getTypeAsString(): string
    {
        if (!$this->reflectionObject->hasType()) {
            $types = array_map(function (string $type) {
                return ltrim($type, '\\');
            }, $this->reflectionObject->getDocBlockTypeStrings());

            return implode('|', $types);
        }

        $mapping = ['int' => 'integer', 'bool' => 'boolean'];
        $originalType = (string)$this->reflectionObject->getType();
        $type = $mapping[$originalType] ?? $originalType;

        return $this->reflectionObject->allowsNull() ? $type . '|null' : $type;
    }
}
