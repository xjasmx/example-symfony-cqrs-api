<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\{Event\UserEmailWasChanged,
    Exception\EmailAlreadyExistException,
    Exception\UserAlreadyExistException,
    Service\UserUniquenessCheckerByEmailInterface,
    Service\UserUniquenessCheckerByIdInterface,
    User,
    ValueObject\Email,
    ValueObject\Name,
    ValueObject\UserId};
use App\Domain\Shared\Exception\DateTimeException;
use App\Tests\AssertEvent;
use PHPUnit\Framework\TestCase;

class UserChangeEmailTest extends TestCase
{
    use AssertEvent;

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
        $oldEmail = (string)$this->user->getEmail();

        $this->user->changeEmail(Email::fromString('new@email.com'), $this->createUniqueEmailSpecification(true));

        self::assertNotNull($this->user->getEmail());
        self::assertNotEquals($oldEmail, (string)$this->user->getEmail());

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
     * @throws DateTimeException
     */
    public function createUser(): void
    {
        $checker = $this->createStub(UserUniquenessCheckerByIdInterface::class);
        $checker->method('isUnique')->willReturn(true);

        $this->user = User::create(
            UserId::fromString($this->id),
            Name::fromString($this->nameFirst, $this->nameLast),
            $this->password,
            $checker
        );
    }

    private function createUniqueEmailSpecification(bool $valid): UserUniquenessCheckerByEmailInterface
    {
        $checker = $this->createStub(UserUniquenessCheckerByEmailInterface::class);
        $checker->method('isUnique')->willReturn($valid);
        return $checker;
    }
}
