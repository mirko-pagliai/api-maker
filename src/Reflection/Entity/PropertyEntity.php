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
use PhpDocMaker\Reflection\Entity\Traits\GetDeclaringClassTrait;
use PhpDocMaker\Reflection\Entity\Traits\GetTypeAsStringTrait;
use PhpDocMaker\Reflection\Entity\Traits\VisibilityTrait;
use Roave\BetterReflection\Reflection\ReflectionProperty;

/**
 * Property entity
 * @method bool isStatic()
 */
class PropertyEntity extends AbstractEntity
{
    use GetDeclaringClassTrait;
    use GetTypeAsStringTrait;
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
        return (string)$this->getDeclaringClass() . '::$' . $this->getName();
    }

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    public function getSignature(): string
    {
        return '$' . $this->getName();
    }
}
