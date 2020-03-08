<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\{
    Exception\UserNotFoundException,
    Repository\UserRepositoryInterface,
    ValueObject\Name,
    ValueObject\UserId
};

class ChangeUserNameHandler
{
    private UserRepositoryInterface $repository;

    /**
     * ChangeUserNameHandler constructor.
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param ChangeUserNameCommand $command
     * @throws UserNotFoundException
     */
    public function handle(ChangeUserNameCommand $command): void
    {
        $user = $this->repository->userOfId(UserId::fromString($command->id));
        $user->changeName(Name::fromString($command->firstName, $command->lastName));

        $this->repository->add($user);
    }
}
