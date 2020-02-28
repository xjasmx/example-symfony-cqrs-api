<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization\OAuth;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\Status;
use App\Domain\IdentityAccess\Identity\ReadModel\UserQueryRepositoryInterface;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Infrastructure\IdentityAccess\Access\Authorization\UserIdentityOAuth;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\UserResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

final class UserResolver implements EventSubscriberInterface
{
    private UserQueryRepositoryInterface $repository;
    private PasswordHasherInterface $hasher;

    /**
     * UserResolver constructor.
     * @param UserQueryRepositoryInterface $repository
     * @param PasswordHasherInterface $hasher
     */
    public function __construct(UserQueryRepositoryInterface $repository, PasswordHasherInterface $hasher)
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
     */
    public function onUserResolve(UserResolveEvent $event): void
    {
        $user = $this->repository->userOfEmail(Email::fromString($event->getUsername()));

        if ($user->getStatus() === Status::WAIT) {
            return;
        }

        if (!$password = $user->getPassword()) {
            return;
        }

        if (!$this->hasher->validate($event->getPassword(), $password)) {
            return;
        }

        $event->setUser(
            new UserIdentityOAuth($user->getId(), $password, $user->getStatus())
        );
    }
}
