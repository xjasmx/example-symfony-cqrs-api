<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;

class ChangeUserEmailHandler
{
    private UserRepositoryInterface $repository;
    private UserUniqueEmailSpecificationInterface $uniqueEmailSpecification;

    /**
     * ChangeEmailHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param UserUniqueEmailSpecificationInterface $uniqueEmailSpecification
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserUniqueEmailSpecificationInterface $uniqueEmailSpecification
    ) {
        $this->repository = $repository;
        $this->uniqueEmailSpecification = $uniqueEmailSpecification;
    }

    /**
     * @param ChangeUserEmailCommand $command
     * @throws UserNotFoundException
     */
    public function handle(ChangeUserEmailCommand $command): void
    {
        $user = $this->repository->userOfId(UserId::fromString($command->id));
        $user->changeEmail(Email::fromString($command->email), $this->uniqueEmailSpecification);

        $this->repository->add($user);
    }
}
