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
namespace PhpDocMaker\Command;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tools\Event\Event;

/**
 * Event subscriber for `PhpDocMakerCommand`
 */
class PhpDocMakerCommandSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected $io;

    /**
     * Returns an array of event names this subscriber wants to listen to
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'classes.founded' => 'onClassFounded',
            'class.rendered' => 'onClassRendered',
            'class.rendering' => 'onClassRendering',
            'functions.founded' => 'onFunctionsFounded',
            'functions.rendered' => 'onFunctionsRendered',
            'functions.rendering' => 'onFunctionsRendering',
            'index.rendered' => 'onIndexRendered',
        ];
    }

    /**
     * Constructor
     * @param \Symfony\Component\Console\Style\SymfonyStyle $io A `SymfonyStyle` instance
     */
    public function __construct(SymfonyStyle $io)
    {
        $this->io = $io;
    }

    /**
     * `classes.founded` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onClassFounded(Event $event): void
    {
        /* @var $classes \PhpDocMaker\Reflection\Entity\ClassEntity */
        $classes = $event->getArg(0);
        $this->io->text(sprintf('Founded %d classes', $classes->count()));
    }

    /**
     * `class.rendered` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onClassRendered(Event $event): void
    {
        /* @var $class \PhpDocMaker\Reflection\Entity\ClassEntity */
        $class = $event->getArg(0);
        $this->io->text(sprintf('Rendered class page for %s', $class->getName()));
    }

    /**
     * `class.rendering` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onClassRendering(Event $event): void
    {
        /* @var $class \PhpDocMaker\Reflection\Entity\ClassEntity */
        $class = $event->getArg(0);
        $this->io->text(sprintf('Rendering class page for %s', $class->getName()));
    }

    /**
     * `functions.founded` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onFunctionsFounded(Event $event): void
    {
        /* @var $functions \Cake\Collection\Collection */
        $functions = $event->getArg(0);
        $this->io->text(sprintf('Founded %d functions', $functions->count()));
    }

    /**
     * `functions.rendered` event
     * @return void
     */
    public function onFunctionsRendered(): void
    {
        $this->io->text(sprintf('Rendered functions page'));
    }

    /**
     * `functions.rendering` event
     * @return void
     */
    public function onFunctionsRendering(): void
    {
        $this->io->text(sprintf('Rendering functions page'));
    }

    /**
     * `index.rendered` event
     * @return void
     */
    public function onIndexRendered(): void
    {
        $this->io->text(sprintf('Rendered index page'));
    }
}
