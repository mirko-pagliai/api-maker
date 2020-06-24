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
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionProperty;

/**
 * Class entity
 * @method ?\Roave\BetterReflection\Reflection\ReflectionClass getParentClass()
 * @method array getImmediateInterfaces()
 * @method bool isAbstract()
 * @method bool isInterface()
 * @method bool isTrait()
 */
class ClassEntity extends AbstractEntity
{
    use DeprecatedTrait;
    use SeeTagsTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionClass
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionClass $class A `ReflectionClass` instance
     */
    public function __construct(ReflectionClass $class)
    {
        $this->reflectionObject = $class;
    }

    /**
     * Gets a constant
     * @param string $name Constant name
     * @return \ApiMaker\Reflection\Entity\ConstantEntity
     */
    public function getConstant(string $name): ConstantEntity
    {
        return new ConstantEntity($this->reflectionObject->getReflectionConstant($name));
    }

    /**
     * Gets all constants as array of `ConstantEntity`
     * @return array
     */
    public function getConstants(): array
    {
        return array_map(function (ReflectionClassConstant $constant) {
            return new ConstantEntity($constant);
        }, $this->reflectionObject->getReflectionConstants());
    }

    /**
     * Returns the link to the page of this class
     * @return string
     */
    public function getLink(): string
    {
        return 'Class-' . $this->getSlug() . '.html';
    }

    /**
     * Gets a method
     * @param string $name Method name
     * @return \ApiMaker\Reflection\Entity\MethodEntity
     */
    public function getMethod(string $name): MethodEntity
    {
        return new MethodEntity($this->reflectionObject->getMethod($name));
    }

    /**
     * Gets all methods as array of `MethodEntity`
     * @return array
     */
    public function getMethods(): array
    {
        return array_map(function (ReflectionMethod $method) {
            return new MethodEntity($method);
        }, $this->reflectionObject->getImmediateMethods());
    }

    /**
     * Gets a property
     * @param string $name Property name
     * @return \ApiMaker\Reflection\Entity\PropertyEntity
     */
    public function getProperty(string $name): PropertyEntity
    {
        return new PropertyEntity($this->reflectionObject->getProperty($name));
    }

    /**
     * Gets all properties as array of `PropertyEntity`
     * @return array
     */
    public function getProperties(): array
    {
        return array_map(function (ReflectionProperty $property) {
            return new PropertyEntity($property);
        }, $this->reflectionObject->getImmediateProperties());
    }

    /**
     * Gets the slug
     * @return string
     */
    public function getSlug(): string
    {
        return str_replace('\\', '-', $this->reflectionObject->getName());
    }
}
