<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

class EmailRegistrationConfirmationCommand
{
    public string $email;
    public string $token;

    /**
     * EmailRegistrationConfirmationCommand constructor.
     * @param string $email
     * @param string $token
     */
    public function __construct(string $email, string $token)
    {
        $this->email = $email;
        $this->token = $token;
    }
}
