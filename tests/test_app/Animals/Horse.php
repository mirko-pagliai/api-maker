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
 * Horse class
 */
final class Horse extends Animal
{
    /**
     * Creates a puppy.
     *
     * This method will return a new `Horse` instance.
     * @return \App\Animals\Horse
     */
    public function createPuppy(): Horse
    {
        return new Horse();
    }

    /**
     * Makes the animal run.
     *
     * In this case, the horse runs faster than the other animals.
     * @return $this
     */
    public function run()
    {
        $this->position = $this->position + 4;

        return $this;
    }
}
