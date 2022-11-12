<?php

namespace App\Client\Controller\Auth;

use App\Infrastructure\Auth\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SocialLoginController extends AbstractController
{
	private const SCOPES = [
		'google' => ['https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/userinfo.profile'],
	];

	private ClientRegistry $clientRegistry;

	public function __construct(ClientRegistry $clientRegistry)
	{
		$this->clientRegistry = $clientRegistry;
	}

	#[Route(path: '/oauth/connect/{service}', name: 'oauth_connect')]
	public function connect(string $service): RedirectResponse
	{
		$this->ensureServiceIsAccepted($service);
		return $this->clientRegistry->getClient($service)->redirect(self::SCOPES[$service]);
	}

	private function ensureServiceIsAccepted(string $service): void
	{
		if (!in_array($service, array_keys(self::SCOPES))) {
			throw new AccessDeniedException();
		}
	}

	#[Route(path: '/oauth/unlink/{service}', name: 'oauth_unlink')]
	#[IsGranted("ROLE_USER")]
	public function disconnect(string $service, AuthService $authService, EntityManagerInterface $em): RedirectResponse
	{
		$this->ensureServiceIsAccepted($service);
		$method = 'set'.ucfirst($service).'Id';
		$authService->getUser()->$method(null);
		$em->flush();
		$this->addFlash('success', 'Votre compte a bien été dissocié de ' . ucwords($service));

		return $this->redirectToRoute('user_edit');
	}

	#[Route(path: '/oauth/check/{service}', name: 'oauth_check')]
	public function check(): Response
	{
		return new Response('errere');
	}
}
