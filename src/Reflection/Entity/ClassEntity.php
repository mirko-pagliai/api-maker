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

use ApiMaker\Reflection\AbstractEntity;
use ApiMaker\Reflection\Entity\Traits\DeprecatedTrait;
use ApiMaker\Reflection\Entity\Traits\SeeTagsTrait;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionProperty;

/**
 * Class entity
 * @method mixed getConstant(string $name)
 * @method ?\Roave\BetterReflection\Reflection\ReflectionClass getParentClass()
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
     * Returns an array of `ConstantEntity`
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
     * @param string $methodName Method name
     * @return \ApiMaker\Reflection\Entity\MethodEntity
     */
    public function getMethod(string $methodName): MethodEntity
    {
        return new MethodEntity($this->reflectionObject->getMethod($methodName));
    }

    /**
     * Returns an array of `MethodEntity`
     * @return array
     */
    public function getMethods(): array
    {
        return array_map(function (ReflectionMethod $method) {
            return new MethodEntity($method);
        }, $this->reflectionObject->getImmediateMethods());
    }

    /**
     * Returns an array of `PropertyEntity`
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
