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
namespace PhpDocMaker;

use Cake\Collection\Collection;
use PhpDocMaker\Reflection\ParentAbstractEntity;

/**
 * ErrorCatcher.
 *
 * This class is used to catch and handle exceptions throwned by entities.
 */
class ErrorCatcher
{
    /**
     * @var array
     */
    private static $errors;

    /**
     * Appends an error
     * @param \PhpDocMaker\Reflection\ParentAbstractEntity $entity The entity to which
     *  the error refers
     * @param string $message Error message
     * @return void
     */
    public static function append(ParentAbstractEntity $entity, string $message): void
    {
        if (preg_match('/^The tag \"(.+)\" does not seem to be wellformed/', $message, $matches)) {
            $message = 'Invalid tag `' . $matches[1] . '`';
        } elseif (string_ends_with($message, 'is not a valid Fqsen.')) {
            $message = 'Invalid tag `' . $entity->render() . '`';
        } elseif ($message === 'Expected a non-empty value. Got: ""') {
            $message = 'Expected a non-empty value';
        }

        $filename = $line = null;
        if (method_exists($entity, 'getFilename')) {
            $filename = $entity->getFilename();
            $line = $entity->getStartLine();
        }

        self::$errors[] = ['entity' => (string)$entity] + compact('message', 'filename', 'line');
    }

    /**
     * Gets all errors
     * @return \Cake\Collection\Collection A collection of errors
     */
    public static function getAll(): Collection
    {
        return collection(array_unique_recursive(self::$errors ?? []));
    }

    /**
     * Resets errors
     * @return void
     */
    public static function reset(): void
    {
        self::$errors = [];
    }
}
