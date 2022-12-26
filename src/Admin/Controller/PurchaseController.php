<?php

namespace App\Admin\Controller;

use App\Application\Purchase\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Purchase\PurchaseRepository;
use App\Application\Purchase\Form\AcceptOrderType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/purchase', name: 'purchase_')]
class PurchaseController extends CrudController
{
	protected string $menuItem = 'purchase';
	protected string $routePrefix = 'admin_purchase';
	private PaginatorInterface $paginator;

	public function __construct(
		private readonly EntityManagerInterface $em,
		PaginatorInterface                      $paginator,
		private readonly UrlGeneratorInterface  $urlGenerator,
	)
	{
		$this->paginator = $paginator;
	}

	#[Route('/{status?}', name: 'index', methods: ['GET'])]
	public function index(Request $request, PurchaseRepository $repository, ?string $status): Response
	{
		$query = $repository->getPurchases($status);

		if ($request->get('q')) {
			$query = $this->applySearch(
				trim($request->get('q')),
				$query,
				[
					'orderNumber',
					'customer.firstname',
					'customer.lastname',
					'phoneNumber',
					'customer.phoneNumber',
				]
			);
		}

		$this->paginator->allowSort('row.orderNumber', 'row.createdAt', 'row.fixedPrice', 'row.total');
		$rows = $this->paginator->paginate($query->getQuery());

		$template = $request->isXmlHttpRequest() ? '_list' : 'index';

		return $this->render("admin/purchase/{$template}.html.twig", [
			'rows' => $rows,
			'searchable' => true,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
			'status' => $status,
		]);
	}

	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function create(Request $request): Response
	{
		$purchase = new Purchase();

		$form = $this->createForm(PurchaseType::class, $purchase)
		             ->add('saveAndCreateNew', SubmitType::class)
		;

		$form->handleRequest($request);

		if ($form->isSubmitted() and $form->isValid()) {

			$this->em->persist($purchase);

			$this->em->flush();
			$this->addFlash('success', 'purchase.created');

			if ($form->get('saveAndCreateNew')->isClicked()) {
				return $this->redirectToRoute($this->routePrefix . '_create');
			}
			return $this->redirectToRoute($this->routePrefix . '_index');
		}

		return $this->renderForm('admin/purchase/create.html.twig', [
			'form' => $form,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
			'purchase' => $purchase,
		]);

	}

	/**
	 * @throws \Exception
	 */
	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Purchase $purchase): Response
	{
		$form = $this->createForm(PurchaseType::class, $purchase);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$this->addFlash('success', 'purchase.updated');

			return $this->redirectToRoute($this->routePrefix . '_index');
		}
		return $this->renderForm('admin/purchase/edit.html.twig', [
			'form' => $form,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
			'purchase' => $purchase,
		]);
	}

	#[Route('/{id}/show', name: 'show')]
	public function show(Purchase $purchase, Request $request): Response
	{
		return $this->render('admin/purchase/show.html.twig', [
			'purchase' => $purchase,
			'customer' => $purchase->getCustomer(),
			'ship' => $purchase->getShip(),
			'invoices' => $purchase->getInvoice(),
			'purchasedItems' => $purchase->getPurchaseProducts(),
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
		]);
	}

	#[Route('/{id}/cancel', name: 'cancel')]
	public function cancel(Purchase $purchase): Response
	{

	}

	#[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
	public function delete(Purchase $purchase, Request $request): Response
	{
		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			$this->addFlash('error', 'invalid_csrf_token');
			$this->back();
		}

		$this->em->remove($purchase);
		$this->em->flush();

		$this->addFlash('success', 'purchase.deleted_successfully');

		return $this->redirect($this->getRedirectUrl($request, $this->urlGenerator));
	}

}
