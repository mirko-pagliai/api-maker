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
use PhpDocMaker\Reflection\Entity\Traits\DeprecatedTrait;
use PhpDocMaker\Reflection\Entity\Traits\SeeTagsTrait;
use PhpDocMaker\Reflection\Entity\Traits\VisibilityTrait;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

/**
 * Constant entity
 */
class ConstantEntity extends AbstractEntity
{
    use DeprecatedTrait;
    use SeeTagsTrait;
    use VisibilityTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionClassConstant
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionClassConstant $constant A `ReflectionClassConstant` instance
     */
    public function __construct(ReflectionClassConstant $constant)
    {
        $this->reflectionObject = $constant;
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        $declaringClass = new ClassEntity($this->reflectionObject->getDeclaringClass());

        return $declaringClass->toSignature() . '::' . $this->reflectionObject->getName();
    }

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    public function toSignature(): string
    {
        return $this->reflectionObject->getName();
    }

    /**
     * Gets the value as string
     * @return string
     */
    public function getValueAsString(): string
    {
        $value = $this->reflectionObject->getValue();

        return is_array($value) ? implode('|', $value) : (string)$value;
    }
}
