<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\IdentityAccess\Identity\Entity;

use App\Domain\IdentityAccess\Identity\ValueObject\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function statusShouldBeActive(): void
    {
        $status = Status::active();

        self::assertTrue($status->isActive());
        self::assertFalse($status->isWait());
    }

    /**
     * @test
     * @group unit
     */
    public function statusShouldBeWait(): void
    {
        $status = Status::wait();

        self::assertTrue($status->isWait());
        self::assertFalse($status->isActive());
    }
}
