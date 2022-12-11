<?php

namespace App\Admin\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends BaseController
{
	#[Route('/', name: 'dashboard')]
	public function dashboard (): Response
	{
		throw new NotFoundHttpException('Please create your dashboard');
		return $this->render('.html.twig');
	}

}
