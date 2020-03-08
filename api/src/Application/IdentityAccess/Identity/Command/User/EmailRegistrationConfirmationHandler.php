<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\{
    Exception\InvalidConfirmationTokenException,
    Exception\UserActivationException,
    Exception\UserNotFoundException,
    Exception\UserPropertyException,
    Repository\UserRepositoryInterface,
    ValueObject\Email};
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime;

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
     * @throws DateTimeException
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserNotFoundException
     * @throws UserPropertyException
     */
    public function handle(EmailRegistrationConfirmationCommand $command): void
    {
        $user = $this->repository->userOfEmail(Email::fromString($command->email));
        $user->confirmRegistrationByEmail($command->token, DateTime::now());

        $this->repository->add($user);
    }
}
