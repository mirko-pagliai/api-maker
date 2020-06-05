<?php

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
namespace App;

use ArrayAccess;

class ArrayExample implements ArrayAccess
{
    public function offsetExists($offset): bool {}
    public function offsetGet($offset) {}
    public function offsetSet($offset, $value): void {}
    public function offsetUnset($offset): void {}
}
