<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Service;

use App\Domain\IdentityAccess\Identity\ValueObject\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\Service\ConfirmTokenizerInterface;
use App\Domain\Shared\ValueObject\DateTime;
use DateInterval;
use DateTimeImmutable;
use Exception;
use Ramsey\Uuid\Uuid;

class RandConfirmTokenizer implements ConfirmTokenizerInterface
{
    private string $interval;

    /**
     * RandConfirmTokenizer constructor.
     * @param string $interval
     */
    public function __construct(string $interval)
    {
        $this->interval = $interval;
    }

    /**
     * @return ConfirmationToken
     * @throws Exception
     */
    public function generate(): ConfirmationToken
    {
        return new ConfirmationToken(
            Uuid::uuid4()->toString(),
            DateTime::fromString(
                (new DateTimeImmutable())->add(new  DateInterval($this->interval))->format(DateTime::FORMAT)
            )
        );
    }
}
