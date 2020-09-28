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

use App\Vehicles\Interfaces\Brake;
use App\Vehicles\Interfaces\MotorVehicle;
use App\Vehicles\Vehicle;

/**
 * A car
 */
class Car extends Vehicle implements Brake, MotorVehicle
{
    /**
     * Possible car types
     */
    protected const TYPES = ['sedan', 'station wagon', 'sport'];

    /**
     * Brake the vehicle
     * @return bool
     * @see \App\Vehicles\Interfaces\Brake
     */
    public function brake(): bool
    {
        //Do something
    }

    /**
     * Starts the vehicle
     * @return bool
     * @see \App\Vehicles\Interfaces\MotorVehicle::start()
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
