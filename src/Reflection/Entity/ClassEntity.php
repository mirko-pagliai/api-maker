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

use Cake\Collection\Collection;
use PhpDocMaker\Reflection\AbstractEntity;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionProperty;
use Roave\BetterReflection\Reflector\Exception\IdentifierNotFound;
use RuntimeException;

/**
 * Class entity
 * @method ?string getFilename()
 * @method string getNamespaceName()
 * @method string getShortName()
 * @method bool isAbstract()
 * @method bool isFinal()
 * @method bool isInterface()
 * @method bool isTrait()
 */
class ClassEntity extends AbstractEntity
{
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
    public function getSignature(): string
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
     * @return \Cake\Collection\Collection A collection of `ConstantEntity`
     */
    public function getConstants(): Collection
    {
        return collection($this->reflectionObject->getImmediateReflectionConstants())->map(function (ReflectionClassConstant $constant) {
            return new ConstantEntity($constant);
        });
    }

    /**
     * Gets all interfaces implemented by this class
     * @return \Cake\Collection\Collection A collection of `ClassEntity`
     */
    public function getInterfaces(): Collection
    {
        return collection($this->reflectionObject->getImmediateInterfaces())->map(function (ReflectionClass $interface) {
            return new ClassEntity($interface);
        });
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
     * @return \Cake\Collection\Collection A collection of `MethodEntity`
     */
    public function getMethods(): Collection
    {
        return collection($this->reflectionObject->getImmediateMethods())->map(function (ReflectionMethod $method) {
            return new MethodEntity($method);
        });
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
     * @return \Cake\Collection\Collection A collection of `PropertyEntity`
     */
    public function getProperties(): Collection
    {
        return collection($this->reflectionObject->getImmediateProperties())->map(function (ReflectionProperty $property) {
            return new PropertyEntity($property);
        });
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
     * Gets all traits used by this class
     * @return \Cake\Collection\Collection A collection of `ClassEntity`
     */
    public function getTraits(): Collection
    {
        return collection($this->reflectionObject->getTraits())->map(function (ReflectionClass $trait) {
            return new ClassEntity($trait);
        });
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

        if ($this->isFinal()) {
            $type = 'Final ' . $type;
        }
        if ($this->hasTag('deprecated')) {
            $type = 'Deprecated ' . $type;
        }

        return $type;
    }
}
