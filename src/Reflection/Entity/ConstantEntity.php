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
namespace ApiMaker\Reflection\Entity;

use ApiMaker\Reflection\AbstractEntity;
use ApiMaker\Reflection\Entity\Traits\DeprecatedTrait;
use ApiMaker\Reflection\Entity\Traits\SeeTagsTrait;
use ApiMaker\Reflection\Entity\Traits\VisibilityTrait;
use Roave\BetterReflection\Reflection\ReflectionClassConstant;

/**
 * Constant entity
 */
class ConstantEntity extends AbstractEntity
{
    use DeprecatedTrait;
    use SeeTagsTrait;
    use VisibilityTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionClassConstant
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionClassConstant $constant A `ReflectionClassConstant` instance
     */
    public function __construct(ReflectionClassConstant $constant)
    {
        $this->reflectionObject = $constant;
    }
}
