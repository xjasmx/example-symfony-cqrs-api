<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Access\Authorization;

use App\Domain\IdentityAccess\Identity\Entity\Status;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
{
    private string $id;
    private string $email;
    private string $password;
    private string $status;

    /**
     * UserIdentity constructor.
     * @param string $id
     * @param string $email
     * @param string $password
     * @param string $status
     */
    public function __construct(
        string $id,
        string $email,
        string $password,
        string $status
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
        return $this->email;
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
        return $this->status === Status::ACTIVE;
    }
}
