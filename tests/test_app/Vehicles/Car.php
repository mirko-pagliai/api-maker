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

use App\Vehicles\MotorVehicle;
use App\Vehicles\Vehicle;

/**
 * A car
 */
class Car extends Vehicle implements MotorVehicle
{
    /**
     * Possible car types
     */
    protected const TYPES = ['sedan', 'station wagon', 'sport'];

    /**
     * Starts the vehicle
     * @return bool
     */
    public function start(): bool
    {
        //Do something
    }

    /**
     * Stops the vehicle
     * @return bool
     */
    public function stop(): bool
    {
        //Do something
    }
}
