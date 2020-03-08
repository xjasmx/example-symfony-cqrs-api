<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Event;

use App\Domain\IdentityAccess\Identity\ValueObject\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use App\Domain\IdentityAccess\Identity\ValueObject\Name;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use App\Domain\Shared\Event\AggregateId;
use App\Domain\Shared\Event\DomainEvent;

class UserWasRegisteredByEmail implements DomainEvent
{
    private UserId $userId;
    private Email $email;
    private ConfirmationToken $confirmToken;
    private Name $name;

    /**
     * UserWasRegisteredByEmail constructor.
     * @param UserId $userId
     * @param Email $email
     * @param Name $name
     * @param ConfirmationToken $confirmToken
     */
    public function __construct(UserId $userId, Email $email, Name $name, ConfirmationToken $confirmToken)
    {
        $this->userId = $userId;
        $this->email = $email;
        $this->name = $name;
        $this->confirmToken = $confirmToken;
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

    /**
     * @return ConfirmationToken
     */
    public function getConfirmToken(): ConfirmationToken
    {
        return $this->confirmToken;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }
}
