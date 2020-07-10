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

use PhpDocMaker\Reflection\AbstractMethodEntity;
use PhpDocMaker\Reflection\Entity\Traits\GetDeclaringClassTrait;
use PhpDocMaker\Reflection\Entity\Traits\VisibilityTrait;
use Roave\BetterReflection\Reflection\ReflectionMethod;

/**
 * Method entity
 */
class MethodEntity extends AbstractMethodEntity
{
    use GetDeclaringClassTrait;
    use VisibilityTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionMethod
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionMethod $reflectionObject A `ReflectionMethod` instance
     */
    public function __construct(ReflectionMethod $reflectionObject)
    {
        $this->reflectionObject = $reflectionObject;
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getDeclaringClass() . '::' . $this->getName() . '()';
    }

    /**
     * Returns `true` if it's static
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->reflectionObject->isStatic();
    }
}
