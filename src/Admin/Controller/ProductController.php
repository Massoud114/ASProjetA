<?php

namespace App\Admin\Controller;

use App\Application\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product', name: 'product_')]
class ProductController extends CrudController
{
	protected string $menuItem = 'product';
	protected string $routePrefix = 'admin_product';
	private PaginatorInterface $paginator;

	public function __construct(
		private readonly EntityManagerInterface $em,
		PaginatorInterface $paginator)
	{
		$this->paginator = $paginator;
	}

	#[Route('/', name: 'index')]
	public function index(Request $request, ProductRepository $repository): Response
	{
		$query = $repository
			->createQueryBuilder('row')
		    ->orderBy('row.createdAt', "DESC");

		if ($request->get('q')) {
			$query = $this->applySearch(trim($request->get('q')), $query, ['name']);
		}

		$this->paginator->allowSort('row.id', 'row.title', 'row.fixedPrice', 'row.createdAt');
		$rows = $this->paginator->paginate($query->getQuery());

		return $this->render("admin/product/index.html.twig", [
			'rows' => $rows,
			'searchable' => true,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
		]);
	}

	#[Route('/create', name: 'create')]
	public function create(): Response
	{
		return $this->crudCreate();
	}

	#[Route('/{id}/edit', name: 'edit')]
	public function edit(Product $product): Response
	{
		return $this->crudEdit($product);
	}

	#[Route('/{id}/delete', name: 'delete')]
	public function delete(Product $product): Response
	{
		return $this->crudDelete($product);
	}

	#[Route('/{id}/show', name: 'show')]
	public function show(Product $product): Response
	{
		return $this->crudShow($product);
	}

	/* Massive delete */
	#[Route('/massive-delete', name: 'massive_delete')]
	public function massiveDelete(): Response
	{
		return $this->crudMassiveDelete();
	}
}
