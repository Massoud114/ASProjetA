<?php

namespace App\Infrastructure\Auth\Exception;

use Throwable;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class NotVerifiedEmailException extends CustomUserMessageAuthenticationException
{
	public function __construct(
		string    $message = "Your account don't have a verified email address. Please verify it before !!!",
		array     $messageData = [],
		int       $code = 0,
		Throwable $previous = null
	)
	{
		parent::__construct($message, $messageData, $code, $previous);
	}
}
