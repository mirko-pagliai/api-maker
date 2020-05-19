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
use ApiMaker\Reflection\Entity\Traits\VisibilityTrait;
use Roave\BetterReflection\Reflection\ReflectionProperty;

/**
 * Property entity
 */
class PropertyEntity extends Entity
{
    use DeprecatedTrait;
    use SeeTagsTrait;
    use VisibilityTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionProperty
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionProperty $property A `ReflectionProperty` instance
     */
    public function __construct(ReflectionProperty $property)
    {
        $this->reflectionObject = $property;
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return '$' . $this->reflectionObject->getName();
    }

    /**
     * Gets type as string. Multiple types will be concatenated
     * @return string
     */
    public function getTypeAsString(): string
    {
        return implode('|', $this->reflectionObject->getDocBlockTypeStrings());
    }
}
