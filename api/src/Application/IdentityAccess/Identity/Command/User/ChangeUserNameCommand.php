<?php

declare(strict_types=1);

namespace App\Application\IdentityAccess\Identity\Command\User;

class ChangeUserNameCommand
{
    public string $id;
    public string $firstName;
    public ?string $lastName;

    /**
     * ChangeUserNameCommand constructor.
     * @param string $id
     * @param string $firstName
     * @param string|null $lastName
     */
    public function __construct(string $id, string $firstName, ?string $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
