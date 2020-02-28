<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

class ChangeUserEmailCommand
{
    public string $id;
    public string $email;

    /**
     * ChangeEmailCommand constructor.
     * @param string $id
     * @param string $email
     */
    public function __construct(string $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
    }
}
