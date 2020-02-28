<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Dispatcher;

use App\Domain\Shared\Event\DomainEvent;
use App\Domain\Shared\Event\EventDispatcherInterface as EventDispatcher;
use App\Infrastructure\Share\Event\Dispatcher\Message\Message;
use Symfony\Component\Messenger\MessageBusInterface;

class MessengerEventDispatcher implements EventDispatcher
{
    private MessageBusInterface $messageBus;

    /**
     * MessengerEventDispatcher constructor.
     * @param MessageBusInterface $messageBus
     */
    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @param array|DomainEvent[] $events
     */
    public function dispatch(array $events): void
    {
        foreach ($events as $event) {
            $this->messageBus->dispatch(new Message($event));
        }
    }
}
