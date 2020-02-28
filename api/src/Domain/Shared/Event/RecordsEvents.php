<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

interface RecordsEvents
{
    /**
     * @return array|DomainEvent[]
     */
    public function releaseEvents(): array;

    /**
     *
     */
    public function clearRecordedEvents(): void;
}
