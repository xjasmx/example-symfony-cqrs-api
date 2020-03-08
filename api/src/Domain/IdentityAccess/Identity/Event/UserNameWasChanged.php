<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Event;

use App\Domain\IdentityAccess\Identity\ValueObject\Name;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use App\Domain\Shared\Event\AggregateId;
use App\Domain\Shared\Event\DomainEvent;

class UserNameWasChanged implements DomainEvent
{
    private UserId $id;
    private Name $name;

    /**
     * UserNameWasChanged constructor.
     * @param UserId $id
     * @param Name $name
     */
    public function __construct(UserId $id, Name $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
