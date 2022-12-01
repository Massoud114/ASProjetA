<?php

namespace App\Client\Controller\Shop;

use App\Application\Shop\Data\SearchData;
use App\Application\Shop\Form\SearchForm;
use App\Client\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Product\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends AbstractController
{
	#[Route('/boutique', name: 'product_index')]
	public function shop(ProductRepository $repo, Request $request): Response
	{
		$data = new SearchData();
		$data->page = $request->get('page', 1);

		$searchForm = $this->createForm(SearchForm::class, $data);
		$searchForm->handleRequest($request);

		[$min, $max] = $repo->findMinMax($data);

		$products = $repo->findSearch($data);

		if ($request->get('ajax')) {
			return new JsonResponse([
				'content' => $this->renderView('shop/product/_products.html.twig', ['products' => $products]),
				'pagination' => $this->renderView('shop/product/_pagination.html.twig', ['products' => $products]),
				'sorting' => $this->renderView('shop/product/_sorting.html.twig', ['products' => $products]),
			]);
		}

		return $this->renderForm('shop/product/index.html.twig', [
			'products' => $products,
			'searchForm' => $searchForm,
			'min' => $min,
			'max' => $max,
		]);
	}
}
