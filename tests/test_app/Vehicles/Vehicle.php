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
namespace App\Vehicles;

/**
 * Vehicle
 */
abstract class Vehicle
{
    /**
     * Color of the vehicle
     * @var string
     */
    protected $color;

    /**
     * Gets the color of this vehicle
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Sets the color of this vehicle
     * @param string $color Color name
     * @return $this
     */
    public function setColor(string $color)
    {
        $this->color = $color;

        return $this;
    }
}
