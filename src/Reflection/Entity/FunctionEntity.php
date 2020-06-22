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

use ApiMaker\Reflection\AbstractFunctionEntity;
use Roave\BetterReflection\Reflection\ReflectionFunction;

/**
 * Function entity
 */
class FunctionEntity extends AbstractFunctionEntity
{
    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionFunction
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
     * Gets the visibility (`public`, `protected` or `private`)
     * @return string
     */
    public function getVisibility(): string
    {
        return '';
    }

    /**
     * Returns `true` if it's static
     * @return bool
     */
    public function isStatic(): bool
    {
        return false;
    }
}
