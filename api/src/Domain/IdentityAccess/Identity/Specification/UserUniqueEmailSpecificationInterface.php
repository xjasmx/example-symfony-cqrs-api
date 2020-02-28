<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Specification;

use App\Domain\IdentityAccess\Identity\Entity\Email;

interface UserUniqueEmailSpecificationInterface
{
    /**
     * @param Email $email
     * @return bool
     */
    public function isUnique(Email $email): bool;
}
