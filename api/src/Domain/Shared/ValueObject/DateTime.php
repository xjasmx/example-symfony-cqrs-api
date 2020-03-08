<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\DateTimeException;

class DateTime
{
    public const FORMAT = 'Y-m-d\TH:i:s.uP';
    private \DateTimeImmutable $dateTime;

    /**
     * @throws DateTimeException
     */
    public static function now(): self
    {
        return self::create();
    }

    /**
     * @param string $dateTime
     * @return DateTime
     * @throws DateTimeException
     */
    public static function fromString(string $dateTime): self
    {
        return self::create($dateTime);
    }

    /**
     * @param string $dateTime
     * @return DateTime
     * @throws DateTimeException
     */
    private static function create(string $dateTime = ''): self
    {
        $self = new self();

        try {
            $self->dateTime = new \DateTimeImmutable($dateTime);
        } catch (\Exception $e) {
            throw new DateTimeException($e);
        }

        return $self;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->dateTime->format(self::FORMAT);
    }

    /**
     * @return \DateTimeImmutable
     */
    public function toNative(): \DateTimeImmutable
    {
        return $this->dateTime;
    }
}
