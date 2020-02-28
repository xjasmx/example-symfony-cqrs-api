<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Service;

interface PasswordHasherInterface
{
    /**
     * @param string $password
     * @return string
     */
    public function hash(string $password): string;

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validate(string $password, string $hash): bool;
}
