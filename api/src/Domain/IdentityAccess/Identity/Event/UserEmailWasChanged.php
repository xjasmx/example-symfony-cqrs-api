<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Event;

use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use App\Domain\Shared\Event\AggregateId;
use App\Domain\Shared\Event\DomainEvent;

class UserEmailWasChanged implements DomainEvent
{
    private UserId $userId;
    private Email $email;

    /**
     * UserEmailWasChanged constructor.
     * @param UserId $userId
     * @param Email $email
     */
    public function __construct(UserId $userId, Email $email)
    {
        $this->userId = $userId;
        $this->email = $email;
    }

    /**
     * @return AggregateId
     */
    public function getAggregateId(): AggregateId
    {
        return $this->userId;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
