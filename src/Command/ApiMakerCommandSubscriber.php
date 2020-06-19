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
namespace ApiMaker\Command;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tools\Event\Event;

/**
 * Event subscriber for `ApiMakerCommand`
 */
class ApiMakerCommandSubscriber implements EventSubscriberInterface
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
            'functions.founded' => 'onFunctionsFounded',
            'functions.rendered' => 'onFunctionsRendered',
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
        $this->io->text(sprintf('Founded %d classes', count($event->getArg(0))));
    }

    /**
     * `class.rendered` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onClassRendered(Event $event): void
    {
        $this->io->text(sprintf('Rendered class page for %s', $event->getArg(0)->getName()));
    }

    /**
     * `functions.founded` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onFunctionsFounded(Event $event): void
    {
        $this->io->text(sprintf('Founded %d functions', count($event->getArg(0))));
    }

    /**
     * `functions.rendered` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onFunctionsRendered(Event $event): void
    {
        $this->io->text(sprintf('Rendered functions page'));
    }

    /**
     * `index.rendered` event
     * @param \Tools\Event\Event $event The `Event` instance
     * @return void
     */
    public function onIndexRendered(Event $event): void
    {
        $this->io->text(sprintf('Rendered index page'));
    }
}