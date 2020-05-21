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
}
