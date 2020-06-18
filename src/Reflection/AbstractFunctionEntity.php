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
namespace ApiMaker\Reflection;

use ApiMaker\Reflection\AbstractEntity;
use ApiMaker\Reflection\Entity\ParameterEntity;
use ApiMaker\Reflection\Entity\Traits\DeprecatedTrait;
use ApiMaker\Reflection\Entity\Traits\SeeTagsTrait;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Roave\BetterReflection\Reflection\ReflectionParameter;

/**
 * AbstractFunctionEntity class.
 *
 * This class contains methods common to functions and class methods.
 */
abstract class AbstractFunctionEntity extends AbstractEntity
{
    use DeprecatedTrait;
    use SeeTagsTrait;

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return $this->reflectionObject->getName() . '(' . $this->getParametersAsString() . ')';
    }

    /**
     * Gets a parameter
     * @param string $parameterName Parameter name
     * @return \ApiMaker\Reflection\Entity\ParameterEntity
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
        return implode(', ', array_map('strval', $this->getParameters()));
    }

    /**
     * Gets return types as string, separated by a comma
     * @return string
     */
    public function getReturnTypeAsString(): string
    {
        $DocBlockInstance = $this->getDocBlockInstance();
        if (!$DocBlockInstance) {
            return '';
        }

        $returnType = array_map(function (Return_ $return) {
            return (string)$return->getType();
        }, $DocBlockInstance->getTagsByName('return'));

        return implode(', ', $returnType);
    }

    /**
     * Gets the return description
     * @return string
     */
    public function getReturnDescription(): string
    {
        $returnTag = $this->getDocBlockInstance()->getTagsByName('return');

        return $this->toHtml($returnTag ? (string)$returnTag[0]->getDescription() : '');
    }

    /**
     * Returns `@throws` tags
     * @return array
     */
    public function getThrowsTags(): array
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? array_map(function (Throws $throws) {
            return [
                'type' => (string)$throws->getType(),
                'description' => (string)$throws->getDescription(),
            ];
        }, $DocBlockInstance->getTagsByName('throws')) : [];
    }
}
