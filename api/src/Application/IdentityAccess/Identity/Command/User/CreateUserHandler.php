<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;

class CreateUserHandler
{
    private UserRepositoryInterface $repository;
    private PasswordHasherInterface $hasher;
    private UserUniqueIdSpecificationInterface $uniqueIdSpecification;

    /**
     * CreateUserHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param UserUniqueIdSpecificationInterface $uniqueIdSpecification
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserUniqueIdSpecificationInterface $uniqueIdSpecification,
        PasswordHasherInterface $hasher
    ) {
        $this->repository = $repository;
        $this->hasher = $hasher;
        $this->uniqueIdSpecification = $uniqueIdSpecification;
    }

    /**
     * @param CreateUserCommand $command
     * @return UserId
     * @throws UserAlreadyExistException
     */
    public function handle(CreateUserCommand $command): UserId
    {
        $user = User::create(
            $userId = $this->repository->nextIdentity(),
            Name::fromString($command->firstName, $command->lastName),
            $this->hasher->hash($command->password),
            new \DateTimeImmutable(),
            $this->uniqueIdSpecification
        );

        $this->repository->add($user);

        return $userId;
    }
}
