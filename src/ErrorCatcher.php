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

use PhpDocMaker\Reflection\ParentAbstractEntity;

/**
 * ErrorCatcher
 */
class ErrorCatcher
{
    /**
     * @var array
     */
    private static $errors;

    /**
     * Appends an error
     * @param \PhpDocMaker\Reflection\ParentAbstractEntity $entity The entity to which the error refers
     * @param string $message Error message
     * @return void
     */
    public static function append(ParentAbstractEntity $entity, string $message): void
    {
        if (preg_match('/^The tag \"(.+)\" does not seem to be wellformed/', $message, $matches)) {
            $message = sprintf('Invalid tag `%s`', $matches[1]);
        } elseif ($message === 'Expected a non-empty value. Got: ""') {
            $message = 'Expected a non-empty value';
        }

        $filename = $entity->getFilename();
        $line = $entity->getStartLine();

        ErrorCatcher::$errors[] = ['entity' => (string)$entity] + compact('message', 'filename', 'line');
    }

    /**
     * Gets all errors
     * @return array
     */
    public static function getAll(): array
    {
        $errors = ErrorCatcher::$errors ?: [];

        return array_values(array_map('unserialize', array_unique(array_map('serialize', $errors))));
    }
}
