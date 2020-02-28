<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization;

use App\Domain\IdentityAccess\Identity\Entity\Status;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentityOAuth implements UserInterface, EquatableInterface
{
    private string $id;
    private string $password;
    private string $status;

    public function __construct(
        string $id,
        string $password,
        string $status
    ) {
        $this->id = $id;
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

    public function getId(): UserId
    {
        return UserId::fromString($this->id);
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
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials(): void
    {
    }

    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }
}
