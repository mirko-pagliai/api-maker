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
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use RuntimeException;

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
     * @throws \RuntimeException
     */
    public function __construct(Tag $tag)
    {
        //Throws an exception for invalid tags
        if ($tag instanceof InvalidTag) {
            preg_match('/^\"(.+)\" is not a valid Fqsen\.$/', $tag->getException()->getMessage(), $matches);

            $message = '@' . $tag->getName();
            $message .= isset($matches[1]) ? ' ' . $matches[1] : '';
            throw new RuntimeException(sprintf('Invalid tag `%s`', $message));
        }

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
    public function toSignature(): string
    {
        return $this->getName();
    }

    /**
     * Returns the tag description
     * @return string
     */
    public function getDescription(): string
    {
        $description = $this->reflectionObject->getDescription();

        if (!$description && method_exists($this->reflectionObject, 'getReference')) {
            $description = $this->reflectionObject->getReference();
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
