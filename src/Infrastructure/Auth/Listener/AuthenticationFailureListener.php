<?php

namespace App\Infrastructure\Auth\Listener;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Infrastructure\Auth\Exception\UserOauthNotFoundException;
use App\Infrastructure\Auth\Exception\UserAuthenticatedException;

class AuthenticationFailureListener implements EventSubscriberInterface
{
	public function __construct(private readonly RequestStack $requestStack, private readonly EntityManagerInterface $entityManager)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [
			LoginFailureEvent::class => 'onAuthenticationFailure',
		];
	}

	public function onAuthenticationFailure(LoginFailureEvent $event)
	{
		$exception = $event->getException();
		if ($exception instanceof UserOauthNotFoundException) {
			$this->userNotFound($exception);
		}

		if ($exception instanceof UserAuthenticatedException) {
			$this->onUserAlreadyAuthenticated($exception);
		}
	}

	public function userNotFound(UserOauthNotFoundException $exception)
	{
		$user = $exception->getResourceOwner();
		$username = $user instanceof GoogleUser ? $user->getName() : $user->getUsername();

		//TODO : Récupérer le locale de l'utilisateur

		$data = [
			'email' => $user->getEmail(),
			'google_id' => $user->getId(),
			'type' => $exception->getService(),
			'username' => $username,
//			'firstname' => $user->getFirstName(),
			'avatar' => $user->getAvatar(),
			'locale' => $user->getLocale(),
		];
		$session = $this->requestStack->getSession();
		$session->set('oauth_login', $data);
	}

	public function onUserAlreadyAuthenticated(UserAuthenticatedException $exception): void
	{
		$resourceOwner = $exception->getResourceOwner();
		$user = $exception->getUser();
		if ($resourceOwner instanceof GoogleUser) {
			$type = 'google';
			$user->setGoogleId($resourceOwner->getId());
		} else {
			$type = 'tiktok';
			$user->setTiktokId($resourceOwner->getId());
		}
		$this->entityManager->flush();
		if ($session = $this->requestStack->getSession()) {
			$session->getFlashBag()->set('success', 'Votre compte a bien été associé à ' . $type);
		}
	}
}
