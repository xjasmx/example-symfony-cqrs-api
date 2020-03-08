<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization\OAuth;

use App\Domain\IdentityAccess\Identity\Exception\UserNotFoundException;
use App\Domain\IdentityAccess\Identity\Repository\UserRepositoryInterface;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use App\Infrastructure\IdentityAccess\Access\Authorization\UserIdentityById;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

final class UserResolver implements EventSubscriberInterface
{
    private UserRepositoryInterface $repository;
    private PasswordHasherInterface $hasher;

    /**
     * UserResolver constructor.
     * @param UserRepositoryInterface $repository
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(UserRepositoryInterface $repository, PasswordHasherInterface $hasher)
    {
        $this->repository = $repository;
        $this->hasher = $hasher;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            OAuth2Events::USER_RESOLVE => 'onUserResolve',
        ];
    }

    /**
     * @param UserResolveEvent $event
     * @throws UserNotFoundException
     */
    public function onUserResolve(UserResolveEvent $event): void
    {
        $user = $this->repository->userOfEmail(Email::fromString($event->getUsername()));

        if ($user->getStatus()->isWait()) {
            return;
        }

        if (!$password = $user->getPassword()) {
            return;
        }

        if (!$this->hasher->validate($event->getPassword(), $password)) {
            return;
        }

        $event->setUser(
            new UserIdentityById($user->getId(), $password, $user->getStatus())
        );
    }
}
