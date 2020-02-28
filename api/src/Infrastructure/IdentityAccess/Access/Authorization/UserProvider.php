<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization;

use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\ReadModel\UserQueryRepositoryInterface;
use App\Domain\IdentityAccess\Identity\ReadModel\UserView;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    private UserQueryRepositoryInterface $repository;

    /**
     * UserProvider constructor.
     * @param UserQueryRepositoryInterface $repository
     */
    public function __construct(UserQueryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username): UserInterface
    {
        /** @var UserView $user */
        $user = $this->repository->userOfId(UserId::fromString($username));

        if (($email = $user->getEmail())) {
            return new UserIdentity(
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
        return UserIdentity::class === $class;
    }
}
