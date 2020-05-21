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
namespace App\Vehicles;

/**
 * A motor vehicle
 */
interface MotorVehicle
{
    /**
     * Starts the vehicle
     * @return bool
     */
    public function start(): bool;

    /**
     * Stops the vehicle
     * @return bool
     */
    public function stop(): bool;
}
