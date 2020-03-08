<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\Shared\ValueObject\DateTime;
use App\Tests\AssertEvent;
use App\Domain\IdentityAccess\Identity\{Event\UserWasCreated,
    Exception\UserActivationException,
    Exception\UserAlreadyExistException,
    Service\UserUniquenessCheckerByEmailInterface,
    Service\UserUniquenessCheckerByIdInterface,
    User,
    ValueObject\Email,
    ValueObject\Name,
    ValueObject\UserId
};
use Exception;
use PHPUnit\Framework\TestCase;

class UserCreationTest extends TestCase
{
    use AssertEvent;

    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function userShouldBedCreatedByConsoleOrAdminPanel(): void
    {
        $user = $this->createUser();
        self::assertNotNull($user);
        self::assertIsObject($user);
        self::assertInstanceOf(User::class, $user);

        self::assertTrue($this->assertEvent($user->releaseEvents(), UserWasCreated::class));
    }

    /**
     * @test
     * @group unit
     * @throws UserAlreadyExistException
     */
    public function ifAtTheTimeOfUserCreationHisIdMatchesTheIdOfAnAlreadyRegisteredUserAnExceptionShouldBeThrown(): void
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

        self::assertNotEmpty($user->getCreatedOn());
        self::assertEmpty($user->getChangedOn());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function statusShouldBeActiveAfterUserCreation(): void
    {
        $user = $this->createUser();

        self::assertNotNull($user->getStatus());
        self::assertTrue($user->isActive());
        self::assertFalse($user->isWait());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function emailCredentialShouldBeEmpty(): void
    {
        $user = $this->createUser();

        self::assertEmpty($user->getEmail());
        self::assertEmpty($user->getConfirmToken());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function emailAddressShouldBeSetWhenAbsent(): void
    {
        $user = $this->createUser();
        $user->changeEmail(Email::fromString($email = 'test@test.com'), $this->createUniqueEmailSpecification(true));

        self::assertNotNull($user->getEmail());
        self::assertSame($email, (string)$user->getEmail());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function shouldBeExceptionWhenTokenIsEmpty(): void
    {
        $this->expectException(UserActivationException::class);

        $user = $this->createUser();

        $user->confirmRegistrationByEmail('confirmHash', DateTime::now());
    }

    /**
     * @param bool $isUnique
     * @return User
     * @throws UserAlreadyExistException
     * @throws Exception
     */
    private function createUser(bool $isUnique = true): User
    {
        return User::create(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            $this->password,
            $this->createUniqueIdSpecification($isUnique)
        );
    }

    private function createUniqueIdSpecification(bool $valid): UserUniquenessCheckerByIdInterface
    {
        $checker = $this->createStub(UserUniquenessCheckerByIdInterface::class);
        $checker->method('isUnique')->willReturn($valid);
        return $checker;
    }

    private function createUniqueEmailSpecification(bool $valid): UserUniquenessCheckerByEmailInterface
    {
        $checker = $this->createStub(UserUniquenessCheckerByEmailInterface::class);
        $checker->method('isUnique')->willReturn($valid);
        return $checker;
    }
}
