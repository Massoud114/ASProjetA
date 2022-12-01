<?php

namespace App\Client\Controller;

use App\Application\User\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

abstract class AbstractController extends SymfonyAbstractController
{
	protected function flashErrors(FormInterface $form): void
	{
		$errors = $form->getErrors();
		$messages = [];
		foreach ($errors as $error){
			$messages[] = $error->getMessage();
		}
		$this->addFlash('error', implode("\n", $messages));
	}

	protected function getUserOrThrow(): User
	{
		$user = $this->getUser();
		if (!($user instanceof User)) {
			throw $this->createAccessDeniedException();
		}

		return $user;
	}

	protected function redirectBack(string $route, array $params = []): RedirectResponse
	{
		/** @var RequestStack $stack */
		$stack = $this->get('request_stack');
		$request = $stack->getCurrentRequest();
		if ($request && $request->server->get('HTTP_REFERER')) {
			return $this->redirect($request->server->get('HTTP_REFERER'));
		}

		return $this->redirectToRoute($route, $params);
	}
}
