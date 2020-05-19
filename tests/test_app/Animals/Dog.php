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
 * Dog class
 */
class Dog extends Animal
{
    /**
     * Number of legs
     */
    protected const LEGS = 4;

    /**
     * Creates a puppy.
     *
     * This method will return a new `Dog` instance
     * @return \App\Animals\Dog
     */
    public function createPuppy(): Dog
    {
        return new Dog();
    }
}
