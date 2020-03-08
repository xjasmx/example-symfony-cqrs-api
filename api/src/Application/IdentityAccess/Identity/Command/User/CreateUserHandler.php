<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\{Repository\UserRepositoryInterface,
    Service\PasswordHasherInterface,
    Service\UserUniquenessCheckerByIdInterface,
    User,
    ValueObject\Name,
    ValueObject\UserId};

class CreateUserHandler
{
    private UserRepositoryInterface $repository;
    private PasswordHasherInterface $hasher;
    private UserUniquenessCheckerByIdInterface $checkerById;

    /**
     * CreateUserHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param UserUniquenessCheckerByIdInterface $checkerById
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserUniquenessCheckerByIdInterface $checkerById,
        PasswordHasherInterface $hasher
    ) {
        $this->repository = $repository;
        $this->hasher = $hasher;
        $this->checkerById = $checkerById;
    }

    /**
     * @param CreateUserCommand $command
     * @return UserId
     * @throws \Exception
     */
    public function handle(CreateUserCommand $command): UserId
    {
        $user = User::create(
            $userId = $this->repository->nextIdentity(),
            Name::fromString($command->firstName, $command->lastName),
            $this->hasher->hash($command->password),
            $this->checkerById
        );

        $this->repository->add($user);

        return $userId;
    }
}
