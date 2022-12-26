<?php

namespace App\Client\Controller\Auth;

use App\Application\User\User;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use App\Application\User\UserRepository;
use App\Infrastructure\Auth\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Infrastructure\Auth\Form\RegistrationFormType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Infrastructure\Auth\Authenticator\LoginFormAuthenticator;
use App\Infrastructure\Auth\Authenticator\Social\GoogleAuthenticator;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{

	public function __construct(private readonly EmailVerifier $emailVerifier)
	{
	}
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

	    $template = $request->isXmlHttpRequest() ? '_login_form' : 'login';
        return $this->render("security/$template.html.twig", ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

	#[Route(path: '/inscription', name: 'app_register')]
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

			$em->persist($user);
			$em->flush();

			// generate a signed url and email it to the user
			$this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
				(new TemplatedEmail())
					->from(new Address('sft@luxdec.bj', 'spider'))
					->to($user->getEmail())
					->subject('Please Confirm your Email')
					->htmlTemplate('registration/confirmation_email.html.twig')
			);
			// do anything else you need here, like send an email

			return $userAuthenticator->authenticateUser($user, $formAuthenticator, $request);
		}

		$template = $request->isXmlHttpRequest() ? '_register_form' : 'register';
		return $this->render("registration/$template.html.twig", [
			'registrationForm' => $form->createView(),
		]);
	}

	#[Route('/verify/email', name: 'app_verify_email')]
	public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
	{
		$id = $request->get('id');

		if (null === $id) {
			return $this->redirectToRoute('app_register');
		}

		$user = $userRepository->find($id);

		if (null === $user) {
			return $this->redirectToRoute('app_register');
		}

		// validate email confirmation link, sets User::isVerified=true and persists
		/*try {
			$this->emailVerifier->handleEmailConfirmation($request, $user);
		} catch (VerifyEmailExceptionInterface $exception) {
			$this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

			return $this->redirectToRoute('app_register');
		}*/

		// @TODO Change the redirect on success and handle or remove the flash message in your templates
		$this->addFlash('success', 'Your email address has been verified.');

		return $this->redirectToRoute('app_register');
	}

}
