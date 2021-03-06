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
namespace App\Animals;

use App\Animals\Animal;
use App\Animals\Traits\ColorsTrait;
use App\Animals\Traits\PositionTrait;
use RuntimeException;

/**
 * Cat class
 * @link https://en.wikipedia.org/wiki/Cat
 * @see \App\Animals\Animal
 */
class Cat extends Animal
{
    use ColorsTrait;
    use PositionTrait;

    /**
     * Genus of this animal.
     *
     * Every animal has its own genus.
     * @deprecated We are no longer interested in knowing the genus of the animal
     * @link https://en.wikipedia.org/wiki/Genus
     * @link https://en.wikipedia.org/wiki/Felis
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
     * Description
     * @var string
     */
    protected static $description;

    /**
     * @deprecated Useless property
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
     * Sets an existing cat as puppy
     * @param \App\Animals\Cat $puppy A `Cat` instance
     * @return void
     */
    public function setPuppy(Cat $puppy): void
    {
        $this->Puppy = $puppy;
    }

    /**
     * Do a meow
     * @param string $meow Type of meow
     * @return void This method returns void
     * @throws RuntimeException With a bad "meow"
     * @link https://en.wikipedia.org/wiki/Meow
     * @see \OtherClass::otherMethod()
     * @since 1.4.0
     */
    public function doMeow(string $meow = 'meow!'): void
    {
        if ($meow === 'woof!') {
            throw new RuntimeException('A cat can\'t do woof');
        }

        //Do a meow here!
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

    /**
     * Returns the animal type
     * @return string
     */
    public static function getType(): string
    {
        return 'cat';
    }
}
