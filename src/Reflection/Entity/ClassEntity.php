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
use PhpDocMaker\Reflection\Entity\Traits\SeeTagsTrait;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionProperty;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use RuntimeException;

/**
 * Class entity
 * @method ?string getFilename()
 * @method array getImmediateInterfaces()
 * @method string getNamespaceName()
 * @method bool isAbstract()
 * @method bool isInterface()
 * @method bool isTrait()
 */
class ClassEntity extends AbstractEntity
{
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
     * Creates a `ClassEntity` from a class name
     * @param string $name Class name
     * @return self
     */
    public static function createFromName(string $name): self
    {
        return new ClassEntity(ReflectionClass::createFromName($name));
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    public function toSignature(): string
    {
        return $this->getName();
    }

    /**
     * Gets a constant
     * @param string $name Constant name
     * @return \PhpDocMaker\Reflection\Entity\ConstantEntity
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
        }, $this->reflectionObject->getImmediateReflectionConstants());
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
     * @return \PhpDocMaker\Reflection\Entity\MethodEntity
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
     * Gets the parent class
     * @return \PhpDocMaker\Reflection\Entity\ClassEntity|null
     * @throws \RuntimeException
     */
    public function getParentClass(): ?ClassEntity
    {
        try {
            $class = $this->reflectionObject->getParentClass();
        } catch (IdentifierNotFound $e) {
            throw new RuntimeException(sprintf('Class `%s` could not be found', $e->getIdentifier()->getName()));
        }

        return $class ? new ClassEntity($class) : null;
    }

    /**
     * Gets a property
     * @param string $name Property name
     * @return \PhpDocMaker\Reflection\Entity\PropertyEntity
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
        return slug($this->getName(), false);
    }

    /**
     * Gets the type class
     * @return string
     */
    public function getType(): string
    {
        $type = 'Class';
        if ($this->isTrait()) {
            $type = 'Trait';
        } elseif ($this->isInterface()) {
            $type = 'Interface';
        } elseif ($this->isAbstract()) {
            $type = 'Abstract';
        }

        if ($this->hasTag('deprecated')) {
            $type = 'Deprecated ' . $type;
        }

        return $type;
    }
}
