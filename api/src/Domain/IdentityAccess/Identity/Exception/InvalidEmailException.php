<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Exception;

class InvalidEmailException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Incorrect email.');
    }
}
