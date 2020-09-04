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
namespace PhpDocMaker\Reflection;

use BadMethodCallException;
use Exception;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlockFactory;
use RuntimeException;
use Tools\Exceptionist;

/**
 * AbstractEntity class
 * @method string getName() Gets the object name
 */
abstract class AbstractEntity
{
    /**
     * `__call()` magic method.
     *
     * It allows access to the methods of the reflected object.
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        Exceptionist::methodExists([$this->reflectionObject, $name], sprintf('Method %s::%s() does not exist', get_class($this->reflectionObject), $name), BadMethodCallException::class);

        return call_user_func_array([$this->reflectionObject, $name], $arguments);
    }

    /**
     * `__toString()` magic method
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Returns the representation of this object as a signature
     * @return string
     */
    abstract public function toSignature(): string;

    /**
     * Internal method to get the `DocBlock` instance
     * @param \Roave\BetterReflection\Reflection\Reflection|\Roave\BetterReflection\Reflection\ReflectionFunctionAbstract|null $reflectionObject A `Reflection` object
     * @return \phpDocumentor\Reflection\DocBlock|null
     */
    protected function getDocBlockInstance($reflectionObject = null): ?DocBlock
    {
        try {
            return DocBlockFactory::createInstance()->create($reflectionObject ?: $this->reflectionObject);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Gets the doc block summary
     * @return string
     */
    public function getDocBlockSummaryAsString(): string
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $DocBlockInstance->getSummary() : '';
    }

    /**
     * Gets the doc block description
     * @return string
     */
    public function getDocBlockDescriptionAsString(): string
    {
        $DocBlockInstance = $this->getDocBlockInstance();

        return $DocBlockInstance ? $DocBlockInstance->getDescription()->render() : '';
    }

    /**
     * Gets the doc block as string
     * @return string
     */
    public function getDocBlockAsString(): string
    {
        $summary = $this->getDocBlockSummaryAsString();
        if (!$summary) {
            return '';
        }

        $description = $this->getDocBlockDescriptionAsString();

        return $summary . ($description ? PHP_EOL . $description : '');
    }

    /**
     * Gets tags by name
     * @param string $name Tags
     * @return array
     * @throws \RuntimeException
     */
    public function getTagsByName($name): array
    {
        $DocBlockInstance = $this->getDocBlockInstance();
        if (!$DocBlockInstance) {
            return [];
        }

        $tags = $DocBlockInstance->getTagsByName($name);

        //Throws an exception for invalid tags
        foreach ($tags as $tag) {
            if (get_class($tag) === InvalidTag::class) {
                preg_match('/^\"(.+)\" is not a valid Fqsen\.$/', $tag->getException()->getMessage(), $matches);

                $message = '@' . $tag->getName();
                if (isset($matches[1])) {
                    $message .= ' ' . $matches[1];
                }

                throw new RuntimeException(sprintf('Invalid tag `%s`', $message));
            }
        }

        return $tags;
    }
}
