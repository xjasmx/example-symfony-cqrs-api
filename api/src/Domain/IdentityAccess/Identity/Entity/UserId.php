<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Entity;

use App\Domain\Shared\Event\AggregateId;

final class UserId implements AggregateId
{
    private string $id;

    /**
     * UserId constructor.
     * @param string $id
     */
    private function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $id
     * @return static
     */
    public static function fromString(string $id): self
    {
        /** @var static $self */
        $self = new self($id);
        return $self;
    }

    /**
     * @param AggregateId $other
     * @return bool
     */
    public function equals(AggregateId $other): bool
    {
        return $other instanceof self && $other->id === $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id;
    }
}
