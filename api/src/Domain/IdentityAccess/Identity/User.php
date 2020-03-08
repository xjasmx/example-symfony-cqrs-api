<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity;

use App\Domain\IdentityAccess\Identity\Event\{UserEmailWasChanged,
    UserNameWasChanged,
    UserPasswordWasChanged,
    UserWasCreated,
    UserWasRegisteredByEmail
};
use App\Domain\IdentityAccess\Identity\Exception\{EmailAlreadyExistException,
    InvalidConfirmationTokenException,
    InvalidCredentialsException,
    UserActivationException,
    UserAlreadyExistException,
    UserPropertyException
};
use App\Domain\IdentityAccess\Identity\Service\{UserUniquenessCheckerByEmailInterface,
    UserUniquenessCheckerByIdInterface
};
use App\Domain\IdentityAccess\Identity\Service\PasswordHasherInterface;
use App\Domain\IdentityAccess\Identity\ValueObject\{ConfirmationToken, Email, Name, Status, UserId};
use App\Domain\Shared\Event\AggregateRoot;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime;

class User extends AggregateRoot
{
    private UserId $id;
    private Name $name;
    private ?Email $email = null;
    private string $password;
    private Status $status;
    private ?ConfirmationToken $confirmToken = null;
    private ?DateTime $changedOn = null;
    private DateTime $createdOn;

    /**
     * User constructor.
     * @param UserId $id
     * @param Name $name
     * @param string $password
     * @param Status $status
     * @throws DateTimeException
     */
    private function __construct(
        UserId $id,
        Name $name,
        string $password,
        Status $status
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
        $this->status = $status;
        $this->createdOn = DateTime::now();
    }

    /**
     * @param UserId $id
     * @param Name $name
     * @param string $password
     * @param UserUniquenessCheckerByIdInterface $checkerById
     * @return static
     * @throws UserAlreadyExistException
     * @throws DateTimeException
     */
    public static function create(
        UserId $id,
        Name $name,
        string $password,
        UserUniquenessCheckerByIdInterface $checkerById
    ): User {
        if (!$checkerById->isUnique($id)) {
            throw new UserAlreadyExistException('User already created.');
        }

        /** @var static $user */
        $user = new self(
            $id,
            $name,
            $password,
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
     * @param UserUniquenessCheckerByEmailInterface $checkerByEmail
     * @return static
     * @throws DateTimeException
     * @throws UserAlreadyExistException
     */
    public static function registerByEmail(
        UserId $id,
        Name $name,
        Email $email,
        string $password,
        ConfirmationToken $token,
        UserUniquenessCheckerByEmailInterface $checkerByEmail
    ): User {
        if (!$checkerByEmail->isUnique($email)) {
            throw new UserAlreadyExistException('User already registered.');
        }

        /** @var static $user */
        $user = new self(
            $id,
            $name,
            $password,
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
     * @param DateTime $expiresDate
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     */
    public function confirmRegistrationByEmail(string $token, DateTime $expiresDate): void
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
     * @param UserUniquenessCheckerByEmailInterface $checkerByEmail
     */
    public function changeEmail(Email $newEmail, UserUniquenessCheckerByEmailInterface $checkerByEmail): void
    {
        if (!$checkerByEmail->isUnique($newEmail)) {
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
    public function getId(): UserId
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Email|null
     */
    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return ConfirmationToken|null
     */
    public function getConfirmToken(): ?ConfirmationToken
    {
        return $this->confirmToken;
    }

    /**
     * @return DateTime|null
     */
    public function getChangedOn(): ?DateTime
    {
        return $this->changedOn;
    }

    /**
     * @return DateTime
     */
    public function getCreatedOn(): DateTime
    {
        return $this->createdOn;
    }
}
