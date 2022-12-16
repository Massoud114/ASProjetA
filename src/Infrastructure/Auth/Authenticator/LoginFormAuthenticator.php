<?php

namespace App\Infrastructure\Auth\Authenticator;

use App\Application\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use App\Infrastructure\Auth\Exception\NotLoginUserException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

	public function start(Request $request, AuthenticationException $authException = null): Response
	{
		if (in_array('application/json', $request->getAcceptableContentTypes()) or in_array('application/ld+json', $request->getAcceptableContentTypes())) {
			return new JsonResponse(null, Response::HTTP_UNAUTHORIZED);
		}
		$url = $this->getLoginUrl($request);

		return new RedirectResponse($url);
	}

	public function __construct(private readonly UrlGeneratorInterface $urlGenerator, private readonly UserRepository $userRepository)
    {
    }

    public function authenticate(Request $request): Passport
    {
	    $username = $request->request->get('username', '');

        $request->getSession()->set(Security::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username, function () use ($username) {
	            $user = $this->userRepository->loadUserByIdentifier($username);
	            if ($user && $user->useOauth()) {
		            throw new NotLoginUserException();
	            }
	            if (!$user) {
		            throw new BadCredentialsException();
	            }
	            return $user;
            }),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
