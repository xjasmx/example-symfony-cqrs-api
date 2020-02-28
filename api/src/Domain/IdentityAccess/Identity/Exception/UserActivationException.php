<?php

declare(strict_types=1);

namespace App\Domain\IdentityAccess\Identity\Exception;

class UserActivationException extends \Exception
{
    public function __construct()
    {
        parent::__construct('User is already active.');
    }
}
