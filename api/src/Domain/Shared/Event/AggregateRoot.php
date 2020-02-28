<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

abstract class AggregateRoot implements RecordsEvents
{
    /**
     * @var array|DomainEvent[]
     */
    private array $recordedEvents = [];

    /**
     * @param DomainEvent $event
     */
    protected function recordEvent(DomainEvent $event): void
    {
        $this->recordedEvents[] = $event;
    }

    /**
     * @return array|DomainEvent[]
     */
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    /**
     *
     */
    public function clearRecordedEvents(): void
    {
        $this->recordedEvents = [];
    }
}
