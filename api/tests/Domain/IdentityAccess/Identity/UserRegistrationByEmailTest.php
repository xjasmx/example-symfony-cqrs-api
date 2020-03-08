<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Tests\AssertEvent;
use App\Domain\IdentityAccess\Identity\{Event\UserWasRegisteredByEmail,
    Exception\UserAlreadyExistException,
    Service\UserUniquenessCheckerByEmailInterface,
    User,
    ValueObject\ConfirmationToken,
    ValueObject\Email,
    ValueObject\Name,
    ValueObject\UserId};
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime;
use Exception;
use PHPUnit\Framework\TestCase;

class UserRegistrationByEmailTest extends TestCase
{
    use AssertEvent;

    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';
    private string $email = 'test@test.com';
    private ConfirmationToken $confirmToken;

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function userShouldBedRegisteredByEmail(): void
    {
        /** @var User $user */
        $user = $this->createUser();
        self::assertNotNull($user);
        self::assertIsObject($user);
        self::assertInstanceOf(User::class, $user);

        self::assertTrue($this->assertEvent($user->releaseEvents(), UserWasRegisteredByEmail::class));
    }

    /**
     * @test
     * @group unit
     * @throws UserAlreadyExistException
     * @throws DateTimeException
     */
    public function ifDuringRegistrationTheUserEmailMatchesTheEmailOfAnAlreadyRegisteredUserAnExceptionShouldBeThrown(): void
    {
        $this->expectException(UserAlreadyExistException::class);
        /** @var User $user */
        $this->createUser(false);
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function credentialShouldBeEqualsInputData(): void
    {
        $user = $this->createUser();

        self::assertSame((string)$user->getId(), $this->id);
        self::assertSame($user->getName()->first(), $this->nameFirst);
        self::assertSame($user->getName()->last(), $this->nameLast);
        self::assertSame($user->getPassword(), $this->password);
        self::assertSame((string)$user->getEmail(), $this->email);
        self::assertSame($user->getConfirmToken()->token(), $this->confirmToken->token());

        self::assertNotEmpty($user->getCreatedOn());
        self::assertEmpty($user->getChangedOn());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function statusShouldBeWaitAfterRegistration(): void
    {
        $user = $this->createUser();

        self::assertNotNull($user->getStatus());
        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }

    /**
     * @param bool $isUnique
     * @return User
     * @throws UserAlreadyExistException
     * @throws DateTimeException
     */
    private function createUser(bool $isUnique = true): User
    {
        return User::registerByEmail(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            Email::fromString($this->email),
            $this->password,
            $this->confirmToken = new ConfirmationToken('confirmHash', DateTime::fromString('+1 day')),
            $this->createUniqueEmailSpecification($isUnique)
        );
    }

    private function createUniqueEmailSpecification(bool $valid): UserUniquenessCheckerByEmailInterface
    {
        $checker = $this->createStub(UserUniquenessCheckerByEmailInterface::class);
        $checker->method('isUnique')->willReturn($valid);
        return $checker;
    }
}
