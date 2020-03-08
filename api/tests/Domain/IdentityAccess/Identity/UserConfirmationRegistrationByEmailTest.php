<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\IdentityAccess\Identity\{Exception\InvalidConfirmationTokenException,
    Exception\UserActivationException,
    Exception\UserAlreadyExistException,
    Exception\UserPropertyException,
    Service\UserUniquenessCheckerByEmailInterface,
    User,
    ValueObject\ConfirmationToken,
    ValueObject\Email,
    ValueObject\Name,
    ValueObject\UserId};
use PHPUnit\Framework\TestCase;

class UserConfirmationRegistrationByEmailTest extends TestCase
{
    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';
    private string $email = 'test@test.com';
    private ConfirmationToken $confirmToken;

    private User $user;

    /**
     * @test
     * @group unit
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     * @throws DateTimeException
     */
    public function statusShouldBeActiveAfterConfirmationToken(): void
    {
        $this->user->confirmRegistrationByEmail('confirmHash', DateTime::now());

        self::assertFalse($this->user->isWait());
        self::assertTrue($this->user->isActive());
    }

    /**
     * @test
     * @group unit
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     * @throws DateTimeException
     */
    public function afterConfirmationOfRegistrationTheTokenShouldBeCleared(): void
    {
        $this->user->confirmRegistrationByEmail('confirmHash', DateTime::now());
        self::assertNull($this->user->getConfirmToken());
    }

    /**
     * @test
     * @group unit
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     * @throws DateTimeException
     */
    public function shouldBeExceptionWhenReconfirmationToken(): void
    {
        $this->expectException(UserActivationException::class);

        $this->user->confirmRegistrationByEmail('confirmHash', DateTime::now());
        $this->user->confirmRegistrationByEmail('confirmHash', DateTime::now());
    }

    /**
     * @before
     * @throws UserAlreadyExistException
     * @throws DateTimeException
     */
    public function createUser(): void
    {
        $checker = $this->createStub(UserUniquenessCheckerByEmailInterface::class);
        $checker->method('isUnique')->willReturn(true);

        $this->user = User::registerByEmail(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            Email::fromString($this->email),
            $this->password,
            $this->confirmToken = new ConfirmationToken('confirmHash', DateTime::fromString('+1 day')),
            $checker
        );
    }
}
