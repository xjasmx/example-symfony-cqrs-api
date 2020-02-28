<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use App\Domain\IdentityAccess\Identity\Exception\UserActivationException;
use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Exception\UserPropertyException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;

class EmailRegistrationConfirmationHandler
{
    private UserRepositoryInterface $repository;

    /**
     * EmailRegistrationConfirmationHandler constructor.
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param EmailRegistrationConfirmationCommand $command
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserNotFoundException
     * @throws UserPropertyException
     */
    public function handle(EmailRegistrationConfirmationCommand $command): void
    {
        $user = $this->repository->userOfEmail(Email::fromString($command->email));
        $user->confirmRegistrationByEmail($command->token, new \DateTimeImmutable());

        $this->repository->add($user);
    }
}
