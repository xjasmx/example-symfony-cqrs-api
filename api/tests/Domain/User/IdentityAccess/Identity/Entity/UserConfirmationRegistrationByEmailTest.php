<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\InvalidConfirmationTokenException;
use App\Domain\IdentityAccess\Identity\Exception\UserActivationException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Exception\UserPropertyException;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use PHPUnit\Framework\TestCase;

class UserConfirmationRegistrationByEmailTest extends TestCase
{
    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';
    private string $email = 'test@test.com';
    private ConfirmationToken $confirmToken;
    private \DateTimeImmutable $date;

    private User $user;

    /**
     * @test
     * @group unit
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     */
    public function statusShouldBeActiveAfterConfirmationToken(): void
    {
        $this->user->confirmRegistrationByEmail('confirmHash', new \DateTimeImmutable());

        self::assertFalse($this->user->isWait());
        self::assertTrue($this->user->isActive());
    }

    /**
     * @test
     * @group unit
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     */
    public function afterConfirmationOfRegistrationTheTokenShouldBeCleared(): void
    {
        $this->user->confirmRegistrationByEmail('confirmHash', new \DateTimeImmutable());
        self::assertNull($this->user->confirmToken());
    }

    /**
     * @test
     * @group unit
     * @throws InvalidConfirmationTokenException
     * @throws UserActivationException
     * @throws UserPropertyException
     */
    public function shouldBeExceptionWhenReconfirmationToken(): void
    {
        $this->expectException(UserActivationException::class);

        $this->user->confirmRegistrationByEmail('confirmHash', new \DateTimeImmutable());
        $this->user->confirmRegistrationByEmail('confirmHash', new \DateTimeImmutable());
    }

    /**
     * @before
     * @throws UserAlreadyExistException
     */
    public function createUser(): void
    {
        $uniqueEmailSpecification = $this->createStub(UserUniqueEmailSpecificationInterface::class);
        $uniqueEmailSpecification->method('isUnique')->willReturn(true);

        $this->user = User::registerByEmail(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            Email::fromString($this->email),
            $this->password,
            $this->confirmToken = new ConfirmationToken('confirmHash', new \DateTimeImmutable('+1 day')),
            $this->date = new \DateTimeImmutable(),
            $uniqueEmailSpecification
        );
    }
}
