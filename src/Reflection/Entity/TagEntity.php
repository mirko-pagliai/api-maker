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
namespace PhpDocMaker\Reflection\Entity;

use PhpDocMaker\Reflection\ParentAbstractEntity;
use phpDocumentor\Reflection\DocBlock\Tag;

/**
 * Tag entity
 */
class TagEntity extends ParentAbstractEntity
{
    /**
     * @var \phpDocumentor\Reflection\DocBlock\Tag
     */
    protected $reflectionObject;

    /**
     * Construct
     * @param \phpDocumentor\Reflection\DocBlock\Tag $tag A `Tag` instance
     */
    public function __construct(Tag $tag)
    {
        $this->reflectionObject = $tag;
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    public function getSignature(): string
    {
        return $this->getName();
    }

    /**
     * Returns the tag description
     * @return string
     * @todo An exception should be throwned if no method is available
     */
    public function getDescription(): string
    {
        $description = '';

        //First it looks for alternative methods to the `getDescription()` method
        foreach (['getLink', 'getReference', 'getVersion'] as $methodToCall) {
            if (method_exists($this->reflectionObject, $methodToCall)) {
                $description = call_user_func([$this->reflectionObject, $methodToCall]);
                break;
            }
        }

        if (!$description && method_exists($this->reflectionObject, 'getDescription')) {
            $description = $this->reflectionObject->getDescription();
        }

        return ltrim((string)$description, '\\');
    }

    /**
     * Returns the tag type
     * @return string
     */
    public function getType(): string
    {
        if (!method_exists($this->reflectionObject, 'getType')) {
            return '';
        }

        return ltrim((string)$this->reflectionObject->getType(), '\\');
    }
}
