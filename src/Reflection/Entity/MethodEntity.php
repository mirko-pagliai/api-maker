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

use ApiMaker\Reflection\AbstractFunctionEntity;
use ApiMaker\Reflection\Entity\Traits\VisibilityTrait;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Roave\BetterReflection\Reflection\ReflectionMethod;

/**
 * Method entity
 */
class MethodEntity extends AbstractFunctionEntity
{
    use VisibilityTrait;

    /**
     * @var \Roave\BetterReflection\Reflection\ReflectionMethod
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \Roave\BetterReflection\Reflection\ReflectionMethod $reflectionObject A `ReflectionMethod` instance
     */
    public function __construct(ReflectionMethod $reflectionObject)
    {
        $this->reflectionObject = $reflectionObject;
    }

    /**
     * Returns `@throws` tags
     * @return array
     */
    public function getThrowsTags(): array
    {
        return array_map(function (Throws $throws) {
            return [
                'type' => (string)$throws->getType(),
                'description' => (string)$throws->getDescription(),
            ];
        }, $this->getDocBlockInstance()->getTagsByName('throws'));
    }
}
