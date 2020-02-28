<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Exception\UserActivationException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;
use Exception;
use PHPUnit\Framework\TestCase;

class UserCreationTest extends TestCase
{
    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';
    private \DateTimeImmutable $date;

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

        self::assertSame((string)$user->id(), $this->id);
        self::assertSame($user->name()->first(), $this->nameFirst);
        self::assertSame($user->name()->last(), $this->nameLast);
        self::assertSame($user->password(), $this->password);
        self::assertSame($user->date(), $this->date);
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function statusShouldBeActiveAfterUserCreation(): void
    {
        $user = $this->createUser();

        self::assertNotNull($user->status());
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

        self::assertEmpty($user->email());
        self::assertEmpty($user->confirmToken());
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

        self::assertNotNull($user->email());
        self::assertSame($email, (string)$user->email());
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

        $user->confirmRegistrationByEmail('confirmHash', new \DateTimeImmutable());
    }

    /**
     * @param bool $isUnique
     * @return User
     * @throws UserAlreadyExistException
     */
    private function createUser(bool $isUnique = true): User
    {
        return User::create(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            $this->password,
            $this->date = new \DateTimeImmutable(),
            $this->createUniqueIdSpecification($isUnique)
        );
    }

    private function createUniqueIdSpecification(bool $valid): UserUniqueIdSpecificationInterface
    {
        $uniqueEmailSpecification = $this->createStub(UserUniqueIdSpecificationInterface::class);
        $uniqueEmailSpecification->method('isUnique')->willReturn($valid);
        return $uniqueEmailSpecification;
    }

    private function createUniqueEmailSpecification(bool $valid): UserUniqueEmailSpecificationInterface
    {
        $uniqueEmailSpecification = $this->createStub(UserUniqueEmailSpecificationInterface::class);
        $uniqueEmailSpecification->method('isUnique')->willReturn($valid);
        return $uniqueEmailSpecification;
    }
}
