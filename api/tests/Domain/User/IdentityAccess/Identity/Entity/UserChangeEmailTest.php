<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\Entity\Email;
use App\Domain\IdentityAccess\Identity\Entity\Name;
use App\Domain\IdentityAccess\Identity\Entity\User;
use App\Domain\IdentityAccess\Identity\Entity\UserId;
use App\Domain\IdentityAccess\Identity\Event\UserEmailWasChanged;
use App\Domain\IdentityAccess\Identity\Exception\EmailAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Exception\UserAlreadyExistException;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueEmailSpecificationInterface;
use App\Domain\IdentityAccess\Identity\Specification\UserUniqueIdSpecificationInterface;
use App\Domain\Shared\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

class UserChangeEmailTest extends TestCase
{
    private string $id = '123456';
    private string $nameFirst = 'First';
    private string $nameLast = 'Last';
    private string $password = 'passwordHash';

    private User $user;

    /**
     * @test
     * @group unit
     */
    public function emailShouldBeChange(): void
    {
        $oldEmail = (string)$this->user->email();

        $this->user->changeEmail(Email::fromString('new@email.com'), $this->createUniqueEmailSpecification(true));

        self::assertNotNull($this->user->email());
        self::assertNotEquals($oldEmail, (string)$this->user->email());

        self::assertTrue($this->assertEvent($this->user->releaseEvents(), UserEmailWasChanged::class));
    }

    /**
     * @test
     * @group unit
     */
    public function shouldBeExceptionWhenSetAnExistingEmail(): void
    {
        $this->expectException(EmailAlreadyExistException::class);

        $this->user->changeEmail(Email::fromString('exist@email.com'), $this->createUniqueEmailSpecification(false));
    }

    /**
     * @before
     * @throws UserAlreadyExistException
     */
    public function createUser(): void
    {
        /** @var User UserUniqueIdSpecificationInterface */
        $uniqueEmailSpecification = $this->createStub(UserUniqueIdSpecificationInterface::class);
        $uniqueEmailSpecification->method('isUnique')->willReturn(true);

        $this->user = User::create(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            $this->password,
            $this->date = new \DateTimeImmutable(),
            $uniqueEmailSpecification
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
