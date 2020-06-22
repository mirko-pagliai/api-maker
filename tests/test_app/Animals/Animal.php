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
namespace App\Animals;

/**
 * Animal abstract class.
 *
 * Other animal classes have to extend this class.
 */
abstract class Animal
{
    /**
     * The name
     * @var string
     */
    protected $name;

    /**
     * Color. It is `null` if the color is unknown
     * @var string|null
     */
    protected $color = null;

    /**
     * Parents. Array with mother and father
     * @var array
     */
    protected $parents = [];

    /**
     * Sets or gets the name of this animal
     * @param string|null $name The name or `null` to get the current name
     * @return mixed Returns the current instance or the name as string
     * @deprecated use instead `getName()`/`setName()`
     */
    public function name(?string $name = null)
    {
        if ($name) {
            return $this->setName($name);
        }

        return $this->getName();
    }

    /**
     * Gets the color of the animal
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * Gets the name of the animal
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the name of this animal
     * @param string $name The name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the parents. Array with mother and father
     * @return array
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    /**
     * Sets the parents
     * @param string $mother Name of the mother
     * @param string $father Name of the father
     * @return $this
     */
    public function setParents(string $mother, string $father)
    {
        $this->parents = [$mother, $father];

        return $this;
    }

    /**
     * Creates a puppy.
     *
     * This method must be implemented by each class.
     */
    abstract public function createPuppy();

    /**
     * Gets the number of legs for the current animal
     * @return int
     * @throws LogicException If the `LEGS` constant is not defined
     */
    public function getLegs(): int
    {
        if (!defined('LEGS')) {
            throw new LogicException('The number of legs for this animal is not known ...');
        }

        return self::LEGS;
    }
}
