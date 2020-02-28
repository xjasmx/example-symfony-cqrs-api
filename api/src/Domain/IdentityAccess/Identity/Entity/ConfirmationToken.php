<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use DateTimeImmutable;

final class ConfirmationToken
{
    private const MIN_LENGTH = 6;
    private ?string $value;
    private ?DateTimeImmutable $expires;

    /**
     * ConfirmationToken constructor.
     * @param string $token
     * @param DateTimeImmutable $expires
     */
    public function __construct(string $token, DateTimeImmutable $expires)
    {
        $this->setToken($token);
        $this->setExpires($expires);
    }

    /**
     * @param string $token
     * @param DateTimeImmutable $date
     * @return bool
     * @throws InvalidConfirmationTokenException
     */
    public function validate(string $token, DateTimeImmutable $date): bool
    {
        if (!$this->isEqualTo($token)) {
            throw new InvalidConfirmationTokenException('Confirmation token is invalid.');
        }
        if ($this->isExpiredTo($date)) {
            throw new InvalidConfirmationTokenException('Confirmation token is invalid.');
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function token(): ?string
    {
        return $this->value;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function expiresDate(): ?DateTimeImmutable
    {
        return $this->expires;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }

    /**
     * @param string $token
     * @return bool
     */
    private function isEqualTo(string $token): bool
    {
        return $this->value === $token;
    }

    /**
     * @param DateTimeImmutable $date
     * @return bool
     */
    private function isExpiredTo(DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    /**
     * @param string $token
     */
    private function setToken(string $token): void
    {
        if (strlen($token) < self::MIN_LENGTH) {
            throw new \InvalidArgumentException(
                \sprintf(
                    'Expected a token to contain at least %2$s characters. Got: %s',
                    $token,
                    self::MIN_LENGTH
                )
            );
        }

        $this->value = $token;
    }

    /**
     * @param DateTimeImmutable $expires
     */
    private function setExpires(DateTimeImmutable $expires): void
    {
        $this->expires = $expires;
    }
}
