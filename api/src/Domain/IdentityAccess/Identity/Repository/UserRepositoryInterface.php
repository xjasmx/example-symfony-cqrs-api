<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Repository;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\Shared\Event\AggregateRoot;

interface UserRepositoryInterface
{
    /**
     * @param UserId $userId
     * @return User
     * @throws UserNotFoundException
     */
    public function userOfId(UserId $userId): User;

    /**
     * @param Email $email
     * @return User
     * @throws UserNotFoundException
     */
    public function userOfEmail(Email $email): User;

    /**
     * @return UserId
     */
    public function nextIdentity(): UserId;

    /**
     * @param AggregateRoot $user
     */
    public function add(AggregateRoot $user): void;
}
