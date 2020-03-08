<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\DomainServices;

use App\Domain\IdentityAccess\Identity\Service\UserUniquenessCheckerByIdInterface;
use App\Domain\IdentityAccess\Identity\ValueObject\UserId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class UserUniquenessCheckerById implements UserUniquenessCheckerByIdInterface
{
    private Connection $connection;

    /**
     * UserUniquenessCheckerById constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param UserId $userId
     * @return bool
     * @throws DBALException
     */
    public function isUnique(UserId $userId): bool
    {
        $stmt = $this->connection->prepare('SELECT COUNT(id) FROM users WHERE id=:id');
        $stmt->execute([':id' => (string)$userId]);

        $data = $stmt->fetch();

        return $data['count'] === 0;
    }
}
