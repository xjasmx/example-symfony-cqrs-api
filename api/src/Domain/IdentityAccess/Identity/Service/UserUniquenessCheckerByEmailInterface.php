<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Service;

use App\Domain\IdentityAccess\Identity\ValueObject\Email;

interface UserUniquenessCheckerByEmailInterface
{
    /**
     * @param Email $email
     * @return bool
     */
    public function isUnique(Email $email): bool;
}
