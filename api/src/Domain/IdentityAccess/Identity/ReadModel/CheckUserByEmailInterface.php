<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\ReadModel;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\UserId;

interface CheckUserByEmailInterface
{
    /**
     * @param Email $email
     * @return UserId|null
     */
    public function existsEmail(Email $email): ?UserId;
}
