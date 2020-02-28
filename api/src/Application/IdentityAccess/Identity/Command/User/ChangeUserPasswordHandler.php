<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;

class ChangeUserPasswordHandler
{
    private UserRepositoryInterface $repository;
    private PasswordHasherInterface $hasher;

    /**
     * ChangePasswordHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(UserRepositoryInterface $repository, PasswordHasherInterface $hasher)
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    /**
     * @param ChangeUserPasswordCommand $command
     * @throws UserNotFoundException
     */
    public function handle(ChangeUserPasswordCommand $command): void
    {
        $user = $this->repository->userOfId(UserId::fromString($command->id));
        $user->changePassword($command->currentPassword, $command->newPassword, $this->hasher);

        $this->repository->add($user);
    }
}
