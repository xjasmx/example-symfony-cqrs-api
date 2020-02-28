<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Dispatcher\Message;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class Handler implements MessageHandlerInterface
{
    private EventDispatcherInterface $dispatcher;

    /**
     * Handler constructor.
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Message $message
     */
    public function __invoke(Message $message): void
    {
        $this->dispatcher->dispatch($message->getEvent());
    }
}
