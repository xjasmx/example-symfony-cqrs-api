<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\ReadModel;

final class UserView
{
    private string $id;
    private string $firstName;
    private string $dateCreate;
    private string $password;
    private string $status;
    private ?string $lastName;
    private ?string $email;

    /**
     * UserView constructor.
     * @param string $id
     * @param string $firstName
     * @param string $status
     * @param string $password
     * @param string $dateCreate
     * @param string|null $lastName
     * @param string|null $email
     */
    public function __construct(
        string $id,
        string $firstName,
        string $status,
        string $password,
        string $dateCreate,
        ?string $lastName,
        ?string $email
    ) {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->dateCreate = $dateCreate;
        $this->status = $status;
        $this->password = $password;
        $this->lastName = $lastName;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getDateCreate(): string
    {
        return $this->dateCreate;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
