<?php

namespace App\Client\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
	#[Route('/a-propos', name: 'about')]
	public function about(): Response
	{
		return $this->render('pages/about.html.twig');
	}

	#[Route('/politique-confidentialite', name: 'confidentialite')]
	public function privacy(): Response
	{
		return $this->render('pages/confidentialite.html.twig');
	}
}
