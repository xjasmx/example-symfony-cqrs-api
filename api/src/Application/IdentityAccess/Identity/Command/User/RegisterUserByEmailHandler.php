<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\{
    Repository\UserRepositoryInterface,
    Service\ConfirmTokenizerInterface,
    Service\PasswordHasherInterface,
    Service\UserUniquenessCheckerByEmailInterface,
    User,
    ValueObject\Email,
    ValueObject\Name,
    ValueObject\UserId
};
use Exception;

class RegisterUserByEmailHandler
{
    private UserRepositoryInterface $repository;
    private ConfirmTokenizerInterface $tokenizer;
    private PasswordHasherInterface $hasher;
    private UserUniquenessCheckerByEmailInterface $checkerByEmail;

    /**
     * RegisterUserByEmailHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param UserUniquenessCheckerByEmailInterface $checkerByEmail
     * @param PasswordHasherInterface $hasher
     * @param ConfirmTokenizerInterface $tokenizer
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserUniquenessCheckerByEmailInterface $checkerByEmail,
        PasswordHasherInterface $hasher,
        ConfirmTokenizerInterface $tokenizer
    ) {
        $this->repository = $repository;
        $this->tokenizer = $tokenizer;
        $this->hasher = $hasher;
        $this->checkerByEmail = $checkerByEmail;
    }

    /**
     * @param RegisterUserByEmailCommand $command
     * @throws Exception
     */
    public function handle(RegisterUserByEmailCommand $command): void
    {
        $user = User::registerByEmail(
            UserId::fromString($command->id),
            Name::fromString($command->firstName, $command->lastName),
            Email::fromString($command->email),
            $this->hasher->hash($command->password),
            $this->tokenizer->generate(),
            $this->checkerByEmail
        );

        $this->repository->add($user);
    }
}
