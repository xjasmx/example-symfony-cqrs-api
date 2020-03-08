<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Service;

use App\Domain\IdentityAccess\Identity\ValueObject\UserId;

interface UserUniquenessCheckerByIdInterface
{
    /**
     * @param UserId $userId
     * @return bool
     */
    public function isUnique(UserId $userId): bool;
}
