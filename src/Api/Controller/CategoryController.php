<?php

namespace App\Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Product\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
	#[Route('/categories', name: 'category_list', methods: ['GET'])]
//	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function index(Request $request, CategoryRepository $repository): JsonResponse
	{
		return $this->json($repository->search($request->get('q')));
	}
}
