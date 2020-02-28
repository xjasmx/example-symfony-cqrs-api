<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Exception\InvalidEmailException;

final class Email
{
    private string $email;

    /**
     * Email constructor.
     * @param string $email
     */
    private function __construct(string $email)
    {
        $this->setEmail($email);
    }

    /**
     * @param string $email
     * @return static
     */
    public static function fromString(string $email): self
    {
        /** @var static $self */
        $self = new self($email);
        return $self;
    }

    /**
     * @param Email $other
     * @return bool
     */
    public function equals(Email $other): bool
    {
        return $other instanceof self && $other->email === $this->email;
    }

    /**
     * @param string $email
     */
    private function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException();
        }

        $this->email = mb_strtolower($email);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->email;
    }
}
