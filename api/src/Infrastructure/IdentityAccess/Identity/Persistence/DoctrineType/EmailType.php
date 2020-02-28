<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Persistence\DoctrineType;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EmailType extends StringType
{
    public const NAME = 'user_user_email';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Email ? (string)$value : $value;
    }

    /**
     * @psalm-suppress MixedArgument
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Email|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? Email::fromString($value) : null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param AbstractPlatform $platform
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
