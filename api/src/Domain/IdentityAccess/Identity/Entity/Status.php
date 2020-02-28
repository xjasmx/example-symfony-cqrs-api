<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Entity;

final class Status
{
    public const WAIT = 'wait';
    public const ACTIVE = 'active';

    private string $type;

    /**
     * Status constructor.
     * @param string $type
     */
    private function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return static
     */
    public static function wait(): self
    {
        /** @var static $self */
        $self = new self(self::WAIT);
        return $self;
    }

    /**
     * @return static
     */
    public static function active(): self
    {
        /** @var static $self */
        $self = new self(self::ACTIVE);
        return $self;
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->type === self::WAIT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->type === self::ACTIVE;
    }
}
