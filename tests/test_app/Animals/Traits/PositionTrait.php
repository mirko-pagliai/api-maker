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
namespace App\Animals\Traits;

/**
 * Position trait.
 *
 * It allows you to manage the position of the animals.
 */
trait PositionTrait
{
    /**
     * Position.
     *
     * This counts the number of steps from the initial position.
     * @var int
     * @see run()
     * @see walk()
     */
    protected $position = 0;

    /**
     * Gets the current position
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Resets the position
     * @return void
     */
    protected function resetPosition(): void
    {
        $this->position = 0;
    }

    /**
     * Makes the animal run
     * @return $this
     */
    public function run()
    {
        $this->position = $this->position + 2;

        return $this;
    }

    /**
     * Makes the animal walk
     * @return $this
     */
    public function walk()
    {
        $this->position++;

        return $this;
    }
}
