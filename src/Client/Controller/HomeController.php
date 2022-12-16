<?php

namespace App\Client\Controller;

use App\Application\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	public function __construct(
		private readonly EntityManagerInterface $em
	) {

	}

	#[Route('/', name: 'app_home')]
    public function index(): Response
    {
		$favoriteProducts = $this->em->getRepository(Product::class)->findBy(['favorite' => true], null, 6);
        return $this->render('pages/home.html.twig', [
			'products' => $favoriteProducts
        ]);
    }
}
