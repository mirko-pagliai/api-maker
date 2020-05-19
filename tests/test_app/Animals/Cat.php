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

use App\Animals\Animal;

/**
 * Cat class
 * @see https://en.wikipedia.org/wiki/Cat
 */
class Cat extends Animal
{
    /**
     * Genus of this animal.
     *
     * Every animal has its own genus.
     * @deprecated
     * @see https://en.wikipedia.org/wiki/Genus
     * @see https://en.wikipedia.org/wiki/Felis
     */
    const GENUS = 'Felis';

    /**
     * Number of legs
     */
    protected const LEGS = 4;

    /**
     * Puppy instance or `null`
     * @var \App\Animals\Cat|null
     */
    protected $Puppy = null;

    /**
     * Colors
     * @var array
     */
    protected $colors = [];

    /**
     * Description
     * @var string
     */
    protected $description;

    /**
     * @deprecated
     * @var bool
     */
    public $isCat = true;

    /**
     * Creates a puppy.
     *
     * This method will return a new `Cat` instance
     * @return \App\Animals\Cat
     */
    public function createPuppy(): Cat
    {
        $this->Puppy = new Cat();

        return $this->Puppy;
    }

    /**
     * Do a meow
     * @param string $meow Type of meow
     * @see https://en.wikipedia.org/wiki/Meow
     */
    public function doMeow(string $meow = 'meow!'): void
    {
        //Do a meow here!
    }

    /**
     * Sets the animal colors
     * @param string|array $colors Array of colors or string
     * @return $this
     */
    public function setColors($colors = [])
    {
        $this->colors = is_string($colors) ? [$colors] : $colors;

        return $this;
    }

    /**
     * Sets the animal description
     * @param string $description
     * @return $this
     */
    public function setDescription($description = 'description of the animal')
    {
        $this->description = $description;

        return $this;
    }
}
