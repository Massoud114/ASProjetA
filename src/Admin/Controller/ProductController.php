<?php

namespace App\Admin\Controller;

use App\Application\Product\Product;
use App\Application\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends CrudController
{
	protected string $templatePath = 'product';
	protected string $menuItem = 'product';
	protected string $entity = Product::class;
	protected string $routePrefix = 'admin_product';
	protected string $searchField = 'name';
	protected string $singular = 'produit';
	protected string $plural = 'produits';

	#[Route('/', name: 'index')]
	public function index(ProductRepository $repository): Response
	{
		$this->paginator->allowSort('count', 'row.id', 'row.name');
		$query = $repository
			->createQueryBuilder('row');

		return $this->crudIndex($query);
	}
}
