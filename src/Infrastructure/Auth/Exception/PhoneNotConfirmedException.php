<?php

namespace App\Infrastructure\Auth\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class PhoneNotConfirmedException extends CustomUserMessageAccountStatusException
{
    public function __construct(
        string $message = 'phone_number.not.confirmed',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
