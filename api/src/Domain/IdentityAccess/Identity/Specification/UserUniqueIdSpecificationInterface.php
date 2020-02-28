<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Specification;

use App\Domain\IdentityAccess\Identity\Entity\UserId;

interface UserUniqueIdSpecificationInterface
{
    /**
     * @param UserId $id
     * @return bool
     */
    public function isUnique(UserId $id): bool;
}
