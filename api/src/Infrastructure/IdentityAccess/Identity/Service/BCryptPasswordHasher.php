<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Service;

use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use RuntimeException;

class BCryptPasswordHasher implements PasswordHasherInterface
{
    private int $cost;

    /**
     * BCryptPasswordHasher constructor.
     * @param int $cost
     */
    public function __construct(int $cost = 12)
    {
        $this->cost = $cost;
    }

    /**
     * @param string $password
     * @return string
     */
    public function hash(string $password): string
    {
        if (strlen($password) < 6) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Expected a password to contain at least %2$s characters. Got: %s',
                    $password,
                    6
                )
            );
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->cost]);

        if ($hash === false) {
            throw new RuntimeException('Unable to generate hash.');
        }

        return $hash;
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function validate(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
