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
namespace ApiMaker\Reflection\Entity;

use ApiMaker\Reflection\Entity;
use ApiMaker\Reflection\Entity\Traits\DeprecatedTrait;
use ApiMaker\Reflection\Entity\Traits\SeeTagsTrait;
use Roave\BetterReflection\Reflection\ReflectionParameter;
use Roave\BetterReflection\Reflection\ReflectionFunction;

/**
 * Function entity
 */
class FunctionEntity extends Entity
{
    use DeprecatedTrait;
    use SeeTagsTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionClass
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionFunction $function A `ReflectionFunction` instance
     */
    public function __construct(ReflectionFunction $function)
    {
        $this->reflectionObject = $function;
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return $this->reflectionObject->getName() . '()';
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
}
