<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization;

use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use App\Domain\IdentityAccess\Identity\ValueObject\Status;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentityByEmail implements UserInterface, EquatableInterface
{
    private UserId $id;
    private Email $email;
    private string $password;
    private Status $status;

    /**
     * UserIdentity constructor.
     * @param UserId $id
     * @param Email $email
     * @param string $password
     * @param Status $status
     */
    public function __construct(
        UserId $id,
        Email $email,
        string $password,
        Status $status
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self) {
            return false;
        }

        return
            $this->id === $user->id &&
            $this->password === $user->password &&
            $this->status === $user->status;
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return [
            'ROLE_USER',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials(): void
    {
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
