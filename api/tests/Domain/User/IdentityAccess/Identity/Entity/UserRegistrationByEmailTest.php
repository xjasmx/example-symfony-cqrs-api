<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Event\UserWasRegisteredByEmail;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use App\Domain\Shared\Event\DomainEvent;
use Exception;
use PHPUnit\Framework\TestCase;

class UserRegistrationByEmailTest extends TestCase
{
    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';
    private string $email = 'test@test.com';
    private ConfirmationToken $confirmToken;
    private \DateTimeImmutable $date;

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

        self::assertSame((string)$user->id(), $this->id);
        self::assertSame($user->name()->first(), $this->nameFirst);
        self::assertSame($user->name()->last(), $this->nameLast);
        self::assertSame($user->password(), $this->password);
        self::assertSame($user->date(), $this->date);
        self::assertSame((string)$user->email(), $this->email);
        self::assertSame($user->confirmToken()->token(), $this->confirmToken->token());
    }

    /**
     * @test
     * @group unit
     * @throws Exception
     */
    public function statusShouldBeWaitAfterRegistration(): void
    {
        $user = $this->createUser();

        self::assertNotNull($user->status());
        self::assertTrue($user->isWait());
        self::assertFalse($user->isActive());
    }

    /**
     * @param bool $isUnique
     * @return User
     * @throws UserAlreadyExistException
     */
    private function createUser(bool $isUnique = true): User
    {
        return User::registerByEmail(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            Email::fromString($this->email),
            $this->password,
            $this->confirmToken = new ConfirmationToken('confirmHash', new \DateTimeImmutable('+1 day')),
            $this->date = new \DateTimeImmutable(),
            $this->createUniqueEmailSpecification($isUnique)
        );
    }

    /**
     * @param DomainEvent[] $recodedEvents
     * @param string $eventClass
     * @return bool
     */
    private function assertEvent(array $recodedEvents, string $eventClass): bool
    {
        foreach ($recodedEvents as $event) {
            if (get_class($event) === $eventClass) {
                return true;
            }
        }

        return false;
    }

    private function createUniqueEmailSpecification(bool $valid): UserUniqueEmailSpecificationInterface
    {
        $uniqueEmailSpecification = $this->createStub(UserUniqueEmailSpecificationInterface::class);
        $uniqueEmailSpecification->method('isUnique')->willReturn($valid);
        return $uniqueEmailSpecification;
    }
}
