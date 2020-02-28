<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Query\User;

final class GetUserCredentialFindByIdQuery
{
    public string $id;

    /**
     * GetUserCredentialFindByIdQuery constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
