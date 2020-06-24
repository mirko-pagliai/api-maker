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
 * Colors trait
 */
trait ColorsTrait
{
    /**
     * Colors
     * @var array
     */
    protected $colors = [];

    /**
     * Sets the animal colors
     * @param string|array $colors Array of colors or string
     * @return $this
     */
    public function setColors($colors = [])
    {
        $this->colors = is_string($colors) ? [$colors] : $colors;

        return $this;
    }
}
