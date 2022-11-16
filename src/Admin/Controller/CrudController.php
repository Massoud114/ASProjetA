<?php

namespace App\Admin\Controller;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use App\Application\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class CrudController extends BaseController
{
	protected string $entity = Product::class;
	protected string $templatePath = 'product';
	protected string $singular = 'product';
	protected string $plural = 'products';
	protected string $menuItem = '';
	protected string $routePrefix = '';
	protected string $searchField = 'title';

	public function __construct(
		protected EntityManagerInterface $em,
		protected PaginatorInterface $paginator,
		private readonly RequestStack $requestStack
	) { }

	public function crudIndex(QueryBuilder $query = null): Response
	{
		/** @var Request $request */
		$request = $this->requestStack->getCurrentRequest();
		$query = $query ?: $this->getRepository()
		                        ->createQueryBuilder('row')
		                        ->orderBy('row.createdAt', 'DESC');

		if ($request->get('q')) {
			$query = $this->applySearch(trim($request->get('q')), $query);
		}
		$this->paginator->allowSort('row.id', 'row.title');
		$rows = $this->paginator->paginate($query->getQuery());

		return $this->render("admin/{$this->templatePath}/index.html.twig", [
			'rows' => $rows,
			'searchable' => true,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
		]);
	}

	public function getRepository(): EntityRepository
	{
		/** @var EntityRepository $repository */
		$repository = $this->em->getRepository($this->entity); /* @phpstan-ignore-line */

		return $repository;
	}

	protected function applySearch(string $search, QueryBuilder $query): QueryBuilder
	{
		return $query
			->where("LOWER(row.{$this->searchField}) LIKE :search")
			->setParameter('search', '%'.strtolower($search).'%');
	}
}
