<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

interface DomainEvent
{
    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId;
}
