<?php

namespace App\Admin\Controller;

use App\Application\Product\Entity\Category;
use App\Application\Product\Form\CategoryType;
use App\Application\Product\Repository\CategoryRepository;
use App\Helper\Paginator\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\Translation\t;

#[Route('/category', name: 'category_')]
#[IsGranted('ROLE_STOCK')]
class CategoryController extends CrudController
{
	protected string $menuItem = 'category';
	protected string $routePrefix = 'admin_category';
	private PaginatorInterface $paginator;

	public function __construct(
		PaginatorInterface $paginator,
	)
	{
		$this->paginator = $paginator;
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(Request $request, CategoryRepository $repository): Response
	{
		$query = $repository
			->createQueryBuilder('row')
			->orderBy('row.createdAt', 'DESC')
		;
		if ($request->get('q')) {
			$query = $this->applySearch(trim($request->get('q')), $query, ['name']);
		}

		$this->paginator->allowSort('row.name');
		$rows = $this->paginator->paginate($query->getQuery());

		$template = $request->isXmlHttpRequest() ? '_list' : 'index';

		return $this->render("admin/category/{$template}.html.twig", [
			'rows' => $rows,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function new(Request $request, CategoryRepository $categoryRepository, SluggerInterface $slugger): Response
	{
		$category = new Category();
		$form = $this->createForm(CategoryType::class, $category)
		             ->add('saveAndCreateNew', SubmitType::class)
		;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$category->setSlug($slugger->slug($category->getName()));
			$categoryRepository->add($category, true);

			if ($request->isXmlHttpRequest()) {
				return new Response(null, 204);
			}
			if ($form->get('saveAndCreateNew')->isClicked()) {
				return $this->redirectToRoute($this->routePrefix . '_create');
			}

			return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
		}

		$template = $request->isXmlHttpRequest() ? '_form' : 'create';

		return $this->renderForm("admin/category/$template.html.twig", [
			'category' => $category,
			'form' => $form,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Category $category, CategoryRepository $categoryRepository, SluggerInterface $slugger): Response
	{
		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$category->setSlug($slugger->slug($category->getName()));
			$categoryRepository->add($category, true);

			if ($request->isXmlHttpRequest()) {
				return new Response(null, 204);
			}
			return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
		}
		$template = $request->isXmlHttpRequest() ? '_form' : 'edit';

		return $this->renderForm("admin/category/$template.html.twig", [
			'category' => $category,
			'form' => $form,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/{id}/delete', name: 'delete', methods: ['POST', 'DELETE'])]
	public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
	{
		if ($request->isXmlHttpRequest()) {
			$request->request->add(json_decode($request->getContent(), true));
			if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
				return $this->json(['title' => 'invalid_csrf_token', 'detail' => 'Invalid Token'], Response::HTTP_FORBIDDEN);
			}
			if ($category->getChildren()->count()) {
				return $this->json(['title' => 'cannot_be_delete', 'detail' => 'Delete Children'], Response::HTTP_FORBIDDEN);
			}

			$categoryRepository->remove($category, true);

			return new JsonResponse([], 200);
		}

		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			$this->addFlash('error', 'invalid_csrf_token');
			return $this->redirectBack($this->routePrefix . '_index', []);
		}
		if ($category->getChildren()->count()) {
			$this->addFlash('error', t('cannot_be_delete') . ' - ' . t('has_children'));
			return $this->redirectBack($this->routePrefix . '_index', []);
		}

		$categoryRepository->remove($category, true);
		$this->addFlash('success', t('category.deleted'));
		return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);

	}
}
