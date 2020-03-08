<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Event;

use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use App\Domain\Shared\Event\AggregateId;
use App\Domain\Shared\Event\DomainEvent;

class UserPasswordWasChanged implements DomainEvent
{
    private UserId $userId;

    /**
     * UserPasswordWasChanged constructor.
     * @param UserId $userId
     */
    public function __construct(UserId $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->userId;
    }
}
