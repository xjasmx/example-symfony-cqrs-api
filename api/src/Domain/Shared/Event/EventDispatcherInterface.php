<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

interface EventDispatcherInterface
{
    /**
     * @param array|DomainEvent[] $events
     */
    public function dispatch(array $events): void;
}
