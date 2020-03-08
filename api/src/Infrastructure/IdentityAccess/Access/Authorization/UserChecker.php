<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization;

use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * @inheritDoc
     */
    public function checkPreAuth(UserInterface $identity): void
    {
        if (!$identity instanceof UserIdentityByEmail) {
            return;
        }

        if (!$identity->isActive()) {
            $exception = new DisabledException('User account is disabled.');
            $exception->setUser($identity);
            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function checkPostAuth(UserInterface $identity): void
    {
        if (!$identity instanceof UserIdentityByEmail) {
            return;
        }
    }
}
