<?php

namespace App\Infrastructure\Auth\Authenticator\Social;

use Exception;
use App\Application\User\User;
use App\Application\User\UserRepository;
use App\Infrastructure\Auth\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Infrastructure\Auth\Exception\UserOauthNotFoundException;
use App\Infrastructure\Auth\Exception\UserAuthenticatedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

abstract class AbstractSocialAuthenticator extends OAuth2Authenticator
{
	protected string $serviceName = '';
	protected EntityManagerInterface $entityManager;
	private ClientRegistry $clientRegistry;
	private RouterInterface $router;
	private AuthService $authService;

	use TargetPathTrait;

	public function __construct(
		ClientRegistry         $clientRegistry,
		EntityManagerInterface $entityManager,
		RouterInterface        $router,
		AuthService            $authService,
	)
	{
		$this->clientRegistry = $clientRegistry;
		$this->entityManager = $entityManager;
		$this->router = $router;
		$this->authService = $authService;
	}

	/**
	 * @throws \Exception
	 */
	public function supports(Request $request): ?bool
	{

		if ('' === $this->serviceName) {
			throw new Exception("You must set a \$serviceName property (for instance 'google', 'tiktok')");
		}
		return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') == $this->serviceName;
	}

	public function authenticate(Request $request): SelfValidatingPassport
	{
		$client = $this->getClient();
		try {
			$accessToken = $this->fetchAccessToken($client);
		} catch (Exception $e) {
			throw new CustomUserMessageAuthenticationException(
				sprintf("Une erreur est survenue lors de la récupération du token d'accès %s", $this->serviceName)
			);
		}
		try {
			$resourceOwner = $this->getResourceOwnerFromCredentials($accessToken);
		} catch (\Exception) {
			throw new CustomUserMessageAuthenticationException(
				sprintf('Une erreur est survenue lors de la communication avec %s', $this->serviceName)
			);
		}

		/**
		 * On a 4 possibilités :
		 *  - l'utilisateur est déjà connecté, on throw la UserAuthenticatedException qui va être catch par la méthode onUserAlreadyAuthenticated
		 *      de la classe AuthenticationFailureListener. Il met juste à jour les id sociaux puis, la onAuthenticationFailure
		 *      va rediriger vers la page edit
		 *  - L'utilisateur n'est pas connecté et son mail Google n'est pas vérifié, on throw la NotVerifiedEmailException
		 *      qui met un message d'erreur et la méthode onAuthenticationFailure redirige vers la page login avec le message d'erreur
		 *  - L'utilisateur n'est pas connecté et son mail Google est vérifié, mais on ne trouve pas l'utilisateur dans la base de données,
		 *      on throw la UserOauthNotFoundException qui va être catch par la méthode onUserNotFound de la classe AuthenticationFailureListener,
		 *      qui va mettre en session les données reçues chez Google, puis la méthode onAuthenticationFailure redirige vers la page register
		 *      avec une variable en session qui indique qu'il s'agit d'oauth et les données reçues chez Google seront
		 *      utilisées pour enregistrer et connecter l'utilisateur
		 *  - L'utilisateur n'est pas connecté et son mail Google est vérifié, et on trouve l'utilisateur dans la base de données,
		 *      on met à jour les id sociaux et on retourne un SelfValidatingPassport
		 */
		return new SelfValidatingPassport(
			new UserBadge($accessToken->getToken(), function () use ($resourceOwner) {

				$user = $this->authService->getUserOrNull();
				if ($user) {
					throw new UserAuthenticatedException($user, $resourceOwner);
				}

				$repository = $this->entityManager->getRepository(User::class);
				$user = $this->getUserFromResourceOwner($resourceOwner, $repository);
				if ($user === null) {
					throw new UserOauthNotFoundException($resourceOwner, $this->serviceName);
				}
				return $user;
			}), [
				new RememberMeBadge()
			]
		);
	}

	protected function getClient(): OAuth2Client
	{
		/** @var OAuth2Client $client */
		$client = $this->clientRegistry->getClient($this->serviceName);

		return $client;
	}

	protected function getResourceOwnerFromCredentials(AccessToken $accessToken): ResourceOwnerInterface
	{
		return $this->getClient()->fetchUserFromToken($accessToken);
	}

	protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UserRepository $userRepository)
	{
		return null;
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
	{
		$request->request->set('_remember_me', '1');

		if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
			return new RedirectResponse($targetPath);
		}

		return new RedirectResponse($this->router->generate('app_test'));
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		if ($exception instanceof UserOauthNotFoundException) {
			return new RedirectResponse($this->router->generate('app_register', ['oauth' => 1]));
		}

		if ($exception instanceof UserAuthenticatedException) {
			return new RedirectResponse($this->router->generate('user_edit'));
		}

		if ($request->hasSession()) {
			// TODO: Revoir l'erreur qui est retourné à cette section
			$request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
		}
		return new RedirectResponse($this->router->generate('app_login'));
	}

	public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
	{
		return new RedirectResponse(
			$this->router->generate('app_login'),
			Response::HTTP_TEMPORARY_REDIRECT
		);
	}


}
