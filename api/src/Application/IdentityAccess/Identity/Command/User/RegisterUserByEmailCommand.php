<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

class RegisterUserByEmailCommand
{
    public string $id;
    public string $email;
    public string $firstName;
    public ?string $lastName;
    public string $password;

    /**
     * RegisterUserByEmailCommand constructor.
     * @param string $id
     * @param string $email
     * @param string $firstName
     * @param string|null $lastName
     * @param string $password
     */
    public function __construct(
        string $id,
        string $email,
        string $firstName,
        ?string $lastName,
        string $password
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->password = $password;
    }
}
