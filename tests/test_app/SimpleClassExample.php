<?php

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
namespace App;

/**
 * A simple class
 */
class SimpleClassExample
{
    /**
     * A constant
     */
    public const CONST_EXAMPLE = 'example';

    /**
     * A property
     * @var string
     */
    public $propertyExample = 'example';

    /**
     * A method
     * @param string $paramExample A parameter
     * @return void
     */
    public function methodExample(string $paramExample = 'example'): void
    {
    }
}
