<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization;

use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserRepositoryInterface $repository;

    /**
     * UserProvider constructor.
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     * @throws UserNotFoundException
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        $user = $this->repository->userOfId(UserId::fromString($username));

        if (($email = $user->getEmail())) {
            return new UserIdentityByEmail(
                $user->getId(),
                $email,
                $user->getPassword(),
                $user->getStatus()
            );
        }

        throw new UsernameNotFoundException('email not found');
    }

    /**
     * @inheritDoc
     * @throws UserNotFoundException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return UserIdentityByEmail::class === $class;
    }
}
