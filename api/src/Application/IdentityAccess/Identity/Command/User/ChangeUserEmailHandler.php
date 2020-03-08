<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\{
    Exception\UserNotFoundException,
    Repository\UserRepositoryInterface,
    Service\UserUniquenessCheckerByEmailInterface,
    ValueObject\Email,
    ValueObject\UserId
};

class ChangeUserEmailHandler
{
    private UserRepositoryInterface $repository;
    private UserUniquenessCheckerByEmailInterface $checker;

    /**
     * ChangeEmailHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param UserUniquenessCheckerByEmailInterface $checker
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserUniquenessCheckerByEmailInterface $checker
    ) {
        $this->repository = $repository;
        $this->checker = $checker;
    }

    /**
     * @param ChangeUserEmailCommand $command
     * @throws UserNotFoundException
     */
    public function handle(ChangeUserEmailCommand $command): void
    {
        $user = $this->repository->userOfId(UserId::fromString($command->id));
        $user->changeEmail(Email::fromString($command->email), $this->checker);

        $this->repository->add($user);
    }
}
