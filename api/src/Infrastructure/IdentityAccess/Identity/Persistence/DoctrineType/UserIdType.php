<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Persistence\DoctrineType;

use App\Domain\IdentityAccess\Identity\Entity\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;

class UserIdType extends GuidType
{
    public const NAME = 'user_user_id';

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return mixed|string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof UserId ? (string)$value : $value;
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return UserId|mixed|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return !empty($value) ? UserId::fromString($value) : null;
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
