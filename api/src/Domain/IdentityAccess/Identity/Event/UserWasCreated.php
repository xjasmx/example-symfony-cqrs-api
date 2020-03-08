<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Event;

use App\Domain\IdentityAccess\Identity\ValueObject\Name;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use App\Domain\Shared\Event\AggregateId;
use App\Domain\Shared\Event\DomainEvent;

class UserWasCreated implements DomainEvent
{
    private UserId $userId;
    private Name $name;

    /**
     * UserWasCreated constructor.
     * @param UserId $userId
     * @param Name $name
     */
    public function __construct(UserId $userId, Name $name)
    {
        $this->userId = $userId;
        $this->name = $name;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->userId;
    }
}
