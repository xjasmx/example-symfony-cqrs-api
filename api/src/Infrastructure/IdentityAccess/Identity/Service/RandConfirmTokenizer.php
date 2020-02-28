<?php

declare(strict_types=1);

namespace App\Infrastructure\IdentityAccess\Identity\Service;

use App\Domain\IdentityAccess\Identity\Entity\ConfirmationToken;
use App\Domain\IdentityAccess\Identity\Service\ConfirmTokenizerInterface;
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
            (new DateTimeImmutable())->add(new  DateInterval($this->interval))
        );
    }
}
