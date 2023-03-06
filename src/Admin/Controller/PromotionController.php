<?php

namespace App\Admin\Controller;

use App\Application\Campaign\Form\PromotionType;
use App\Application\Campaign\Promotion;
use App\Application\Campaign\PromotionRepository;
use App\Helper\Paginator\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Translation\t;

#[Route('/promotion', name: 'promotion_')]
#[IsGranted('ROLE_STOCK')]
class PromotionController extends CrudController
{
	protected string $menuItem = 'promotion';
	protected string $routePrefix = 'admin_promotion';
	private PaginatorInterface $paginator;

	public function __construct(
		PaginatorInterface $paginator,
	){
		$this->paginator = $paginator;
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(Request $request, PromotionRepository $repository): Response
	{
		$query = $repository
			->createQueryBuilder('row')
			->orderBy('row.createdAt', 'DESC')
		;
		if ($request->get('q')) {
			$query = $this->applySearch(trim($request->get('q')), $query, ['promoCode']);
		}

		$this->paginator->allowSort('row.percentage', 'startAt', 'endAt', 'createdAt');
		$rows = $this->paginator->paginate($query->getQuery());

		$template = $request->isXmlHttpRequest() ? '_list' : 'index';

		return $this->render("admin/promotion/{$template}.html.twig", [
			'rows' => $rows,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function new(Request $request, PromotionRepository $promotionRepository): Response
	{
		$promotion = new Promotion();
		$form = $this->createForm(PromotionType::class, $promotion);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$promotionRepository->add($promotion, true);

			return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm("admin/promotion/create.html.twig", [
			'promotion' => $promotion,
			'form' => $form,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Promotion $promotion, PromotionRepository $promotionRepository): Response
	{
		$form = $this->createForm(PromotionType::class, $promotion);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$promotionRepository->add($promotion, true);

			return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
		}
		return $this->renderForm("admin/promotion/edit.html.twig", [
			'promotion' => $promotion,
			'form' => $form,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/{id}/delete', name: 'delete', methods: ['POST', 'DELETE'])]
	public function delete(Request $request, Promotion $promotion, PromotionRepository $promotionRepository): Response
	{
		if (!$this->isCsrfTokenValid('delete-promotion', $request->request->get('token'))) {
			$this->addFlash('error', 'invalid_csrf_token');
			return $this->redirectBack($this->routePrefix . '_index', []);
		}

		$promotionRepository->remove($promotion, true);
		$this->addFlash('success', t('promotion.deleted'));
		return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);

	}
}
