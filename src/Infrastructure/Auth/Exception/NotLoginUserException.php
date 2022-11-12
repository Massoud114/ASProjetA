<?php

namespace App\Infrastructure\Auth\Exception;

use Throwable;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class NotLoginUserException extends CustomUserMessageAuthenticationException
{
	public function __construct(
		string    $message = 'social.associated.error',
		array     $messageData = [],
		int       $code = 0,
		Throwable $previous = null
	)
	{
		parent::__construct($message, $messageData, $code, $previous);
	}
}
