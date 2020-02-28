<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Service;

use App\Domain\IdentityAccess\Identity\Entity\ConfirmationToken;

interface ConfirmTokenizerInterface
{
    /**
     * @return ConfirmationToken
     */
    public function generate(): ConfirmationToken;
}
