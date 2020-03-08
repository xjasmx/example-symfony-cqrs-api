<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\DomainServices;

use App\Domain\IdentityAccess\Identity\Service\UserUniquenessCheckerByEmailInterface;
use App\Domain\IdentityAccess\Identity\ValueObject\Email;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class UserUniquenessCheckerByEmail implements UserUniquenessCheckerByEmailInterface
{
    private Connection $connection;

    /**
     * UserUniquenessCheckerByEmail constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Email $email
     * @return bool
     * @throws DBALException
     */
    public function isUnique(Email $email): bool
    {
        $stmt = $this->connection->prepare('SELECT COUNT(id) FROM users WHERE email=:email');
        $stmt->execute([':email' => (string)$email]);

        $data = $stmt->fetch();

        return $data['count'] === 0;
    }
}
