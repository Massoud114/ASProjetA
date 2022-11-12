<?php

namespace App\Infrastructure\Auth\Authenticator\Social;

use RuntimeException;
use App\Application\User\User;
use App\Application\User\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use App\Infrastructure\Auth\Exception\NotVerifiedEmailException;

class GoogleAuthenticator extends AbstractSocialAuthenticator
{
	protected string $serviceName = 'google';

	protected function getUserFromResourceOwner(ResourceOwnerInterface $googleUser, UserRepository $userRepository): ?User
	{
		if (!$googleUser instanceof GoogleUser) {
			throw new RuntimeException('Expecting GoogleUser as the first parameter');
		}

		if (true !== ($googleUser->toArray()['email_verified'] ?? null)) {
			throw new NotVerifiedEmailException();
		}

		$user = $userRepository->findForOauth('google', $googleUser->getId(), $googleUser->getEmail());
		if ($user && null === $user->getGoogleId()) {
			$user->setGoogleId($googleUser->getId());
			$this->entityManager->flush();
		}
		return $user;
	}

}
