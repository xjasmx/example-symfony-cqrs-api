<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Dispatcher\Message;

class Message
{
    private object $event;

    /**
     * Message constructor.
     * @param object $event
     */
    public function __construct(object $event)
    {
        $this->event = $event;
    }

    /**
     * @return object
     */
    public function getEvent(): object
    {
        return $this->event;
    }
}
