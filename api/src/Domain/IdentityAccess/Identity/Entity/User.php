<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Event\UserEmailWasChanged;
use App\Domain\IdentityAccess\Identity\Event\UserNameWasChanged;
use App\Domain\IdentityAccess\Identity\Event\UserPasswordWasChanged;
use App\Domain\IdentityAccess\Identity\Event\UserWasRegisteredByEmail;
use App\Domain\IdentityAccess\Identity\Event\UserWasCreated;
use App\Domain\IdentityAccess\Identity\Exception\EmailAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use App\Domain\IdentityAccess\Identity\Exception\InvalidCredentialsException;
use App\Domain\IdentityAccess\Identity\Exception\UserActivationException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Exception\UserPropertyException;
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;
use App\Domain\Shared\Event\AggregateRoot;
use DateTimeImmutable;

class User extends AggregateRoot
{
    private UserId $id;
    private Name $name;
    private ?Email $email = null;
    private string $password;
    private Status $status;
    private ?ConfirmationToken $confirmToken = null;
    private DateTimeImmutable $date;

    /**
     * User constructor.
     * @param UserId $id
     * @param Name $name
     * @param string $password
     * @param DateTimeImmutable $date
     * @param Status $status
     */
    private function __construct(
        UserId $id,
        Name $name,
        string $password,
        DateTimeImmutable $date,
        Status $status
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->date = $date;
        $this->status = $status;
    }

    /**
     * @param UserId $id
     * @param Name $name
     * @param string $password
     * @param DateTimeImmutable $date
     * @param UserUniqueIdSpecificationInterface $uniqueIdSpecification
     * @return static
     * @throws UserAlreadyExistException
     */
    public static function create(
        UserId $id,
        Name $name,
        string $password,
        DateTimeImmutable $date,
        UserUniqueIdSpecificationInterface $uniqueIdSpecification
    ): User {
        if (!$uniqueIdSpecification->isUnique($id)) {
            throw new UserAlreadyExistException('User already created.');
        }

        /** @var static $user */
        $user = new self(
            $id,
            $name,
            $password,
            $date,
            Status::active()
        );

        $user->recordEvent(
            new UserWasCreated(
                $id,
                $name,
            )
        );

        return $user;
    }

    /**
     * @param UserId $id
     * @param Name $name
     * @param Email $email
     * @param string $password
     * @param ConfirmationToken $token
     * @param DateTimeImmutable $date
     * @param UserUniqueEmailSpecificationInterface $uniqueEmailSpecification
     * @return static
     * @throws UserAlreadyExistException
     */
    public static function registerByEmail(
        UserId $id,
        Name $name,
        Email $email,
        string $password,
        ConfirmationToken $token,
        DateTimeImmutable $date,
        UserUniqueEmailSpecificationInterface $uniqueEmailSpecification
    ): User {
        if (!$uniqueEmailSpecification->isUnique($email)) {
            throw new UserAlreadyExistException('User already registered.');
        }

        /** @var static $user */
        $user = new self(
            $id,
            $name,
            $password,
            $date,
            Status::wait()
        );

        $user->email = $email;
        $user->confirmToken = $token;

        $user->recordEvent(
            new UserWasRegisteredByEmail(
                $id,
                $email,
                $name,
                $token
            )
        );

        return $user;
    }

    /**
     * @param string $token
     * @param DateTimeImmutable $expiresDate
     * @throws UserActivationException
     * @throws UserPropertyException
     * @throws InvalidConfirmationTokenException
     */
    public function confirmRegistrationByEmail(string $token, DateTimeImmutable $expiresDate): void
    {
        if ($this->isActive()) {
            throw new UserActivationException();
        }

        if ($this->confirmToken === null) {
            throw new UserPropertyException('User does not have a confirm token.');
        }

        $this->confirmToken->validate($token, $expiresDate);
        $this->status = Status::active();
        $this->confirmToken = null;
    }

    /**
     * @param Name $name
     */
    public function changeName(Name $name): void
    {
        $this->name = $name;

        $this->recordEvent(
            new UserNameWasChanged(
                $this->id,
                $this->name
            )
        );
    }

    /**
     * @param Email $newEmail
     * @param UserUniqueEmailSpecificationInterface $uniqueEmailSpecification
     */
    public function changeEmail(Email $newEmail, UserUniqueEmailSpecificationInterface $uniqueEmailSpecification): void
    {
        if (!$uniqueEmailSpecification->isUnique($newEmail)) {
            throw new EmailAlreadyExistException();
        }

        $this->email = $newEmail;

        $this->recordEvent(
            new UserEmailWasChanged(
                $this->id,
                $this->email
            )
        );
    }

    /**
     * @param string $currentPassword
     * @param string $newPassword
     * @param PasswordHasherInterface $hasher
     */
    public function changePassword(string $currentPassword, string $newPassword, PasswordHasherInterface $hasher): void
    {
        if (!$this->password) {
            throw new InvalidCredentialsException('User does not have a password.');
        }
        if (!$hasher->validate($currentPassword, $this->password)) {
            throw new InvalidCredentialsException('Incorrect current password.');
        }

        $this->password = $hasher->hash($newPassword);

        $this->recordEvent(
            new UserPasswordWasChanged(
                $this->id,
            )
        );
    }

    /**
     * @return bool
     */
    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    /**
     * @return UserId
     */
    public function id(): UserId
    {
        return $this->id;
    }

    /**
     * @return Email|null
     */
    public function email(): ?Email
    {
        return $this->email;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return ConfirmationToken|null
     */
    public function confirmToken(): ?ConfirmationToken
    {
        return $this->confirmToken;
    }

    /**
     * @return DateTimeImmutable
     */
    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function password(): string
    {
        return $this->password;
    }

    /**
     * @return Status
     */
    public function status(): Status
    {
        return $this->status;
    }
}
