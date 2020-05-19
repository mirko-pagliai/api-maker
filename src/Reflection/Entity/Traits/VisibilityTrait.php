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
namespace ApiMaker\Reflection\Entity\Traits;

/**
 * VisibilityTrait
 */
trait VisibilityTrait
{
    /**
     * Gets the visibility (`public`, `protected` or `private`)
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->reflectionObject->isPrivate() ? 'private' : ($this->reflectionObject->isProtected() ? 'protected' : 'public');
    }
}
