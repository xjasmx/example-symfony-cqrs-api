<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Shared\Event\DomainEvent;

trait AssertEvent
{
    /**
     * @param DomainEvent[] $recodedEvents
     * @param string $eventClass
     * @return bool
     */
    private function assertEvent(array $recodedEvents, string $eventClass): bool
    {
        foreach ($recodedEvents as $event) {
            if (get_class($event) === $eventClass) {
                return true;
            }
        }

        return false;
    }
}
