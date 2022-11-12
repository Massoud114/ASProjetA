<?php

namespace App\Client\Controller\Auth;

use App\Application\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Infrastructure\Auth\Authenticator\LoginFormAuthenticator;
use App\Infrastructure\Auth\Authenticator\Social\GoogleAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

	#[Route(path: '/register', name: 'app_register')]
	public function register(
		Request                     $request,
		UserPasswordHasherInterface $passwordHasher,
		UserAuthenticatorInterface  $userAuthenticator,
		LoginFormAuthenticator      $formAuthenticator,
		RequestStack                $requestStack,
		GoogleAuthenticator         $googleAuthenticator,
		EntityManagerInterface	    $em,
	): Response
	{
		if ($this->getUser()) {
			// TODO : Change to user profile
			return $this->redirectToRoute('app_home');
		}

		$user = new User();
		if ($request->get('oauth')) {
			$session = $requestStack->getSession();
			$oauthData = $session->get('oauth_login');

			if (isset($oauthData['email']) || null !== $oauthData) {
				$user   ->setEmail($oauthData['email'])
				        ->setUsername($oauthData['username'])
						->setPassword('')
						->setRoles(['ROLE_USER'])
				        ->setGoogleId($oauthData['google_id'] ?? null)
				        ->setTikTokId($oauthData['tiktok_id'] ?? null)
						->setFirstname($oauthData['username'] ?? null)
						->setAvatarName($oauthData['avatar'] ?? null)
				        ->agreeToTerms();

				$this->addFlash(
					'success',
					'Your account have been successfully created'
				);
				$em->persist($user);
				$em->flush();
				return
					$oauthData['type'] === 'google' ?
						$userAuthenticator->authenticateUser($user, $googleAuthenticator, $request)
						: $userAuthenticator->authenticateUser($user, $formAuthenticator, $request);
			}
		}

		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// encode the plain password
			$user->setPassword(
				$passwordHasher->hashPassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);

			$user->agreeToTerms();

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			// TODO : do anything else you need here, like send an email

			return $userAuthenticator->authenticateUser($user, $formAuthenticator, $request);
		}

		return $this->render('security/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}
}
