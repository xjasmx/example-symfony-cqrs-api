<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\ReadModel;

use App\Domain\IdentityAccess\Identity\Entity\UserId;

interface CheckUserByIdInterface
{
    /**
     * @param UserId $id
     * @return UserId|null
     */
    public function existsId(UserId $id): ?UserId;
}
