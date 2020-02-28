<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\IdentityAccess\Identity\Service\ConfirmTokenizerInterface;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use Exception;

class RegisterUserByEmailHandler
{
    private UserRepositoryInterface $repository;
    private ConfirmTokenizerInterface $tokenizer;
    private PasswordHasherInterface $hasher;
    private UserUniqueEmailSpecificationInterface $uniqueEmailSpecification;

    /**
     * RegisterUserByEmailHandler constructor.
     * @param UserRepositoryInterface $repository
     * @param UserUniqueEmailSpecificationInterface $uniqueEmailSpecification
     * @param PasswordHasherInterface $hasher
     * @param ConfirmTokenizerInterface $tokenizer
     */
    public function __construct(
        UserRepositoryInterface $repository,
        UserUniqueEmailSpecificationInterface $uniqueEmailSpecification,
        PasswordHasherInterface $hasher,
        ConfirmTokenizerInterface $tokenizer
    ) {
        $this->repository = $repository;
        $this->tokenizer = $tokenizer;
        $this->hasher = $hasher;
        $this->uniqueEmailSpecification = $uniqueEmailSpecification;
    }

    /**
     * @param RegisterUserByEmailCommand $command
     * @throws UserAlreadyExistException
     */
    public function handle(RegisterUserByEmailCommand $command): void
    {
        $user = User::registerByEmail(
            UserId::fromString($command->id),
            Name::fromString($command->firstName, $command->lastName),
            Email::fromString($command->email),
            $this->hasher->hash($command->password),
            $this->tokenizer->generate(),
            new \DateTimeImmutable(),
            $this->uniqueEmailSpecification
        );

        $this->repository->add($user);
    }
}
