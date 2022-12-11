<?php

namespace App\Admin\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Helper\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Settings\SocialLink\SocialLink;
use App\Application\Settings\SocialLink\SocialLinkType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Application\Settings\SocialLink\SocialLinkConfType;
use App\Application\Settings\SocialLink\SocialLinkRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use function Symfony\Component\Translation\t;

#[
	Route('/social_links', name: 'social_links_'),
	IsGranted('ROLE_ADMIN')
]
class SocialLinkController extends CrudController
{
	protected string $menuItem = 'social_links';
	protected string $routePrefix = 'admin_social_links';
	private PaginatorInterface $paginator;
	
	public function __construct(
		private readonly EntityManagerInterface $em,
		PaginatorInterface $paginator,
	) {
		$this->paginator = $paginator;
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(Request $request, SocialLinkRepository $repository): Response
	{
		$query = $repository
			->createQueryBuilder('row')
			->orderBy('row.name', 'ASC')
		;

		$rows = $query->getQuery()->getResult();

		$template = $request->isXmlHttpRequest() ? '_list' : 'index';

		return $this->render("admin/social_links/{$template}.html.twig", [
			'rows' => $rows,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function new(Request $request, SocialLinkRepository $repository): Response
	{
		$socialLink = new SocialLink();
		$form = $this->createForm(SocialLinkType::class, $socialLink)
		             ->add('saveAndCreateNew', SubmitType::class, ['label' => 'save_and_create_new'])
		;
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$repository->add($socialLink, true);

			if ($form->get('saveAndCreateNew')->isClicked()) {
				return $this->redirectToRoute($this->routePrefix . '_create');
			}
			$this->addFlash('success', 'element.added');
			return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('admin/social_links/create.html.twig', [
			'socialLink' => $socialLink,
			'form' => $form,
			'prefix' => $this->routePrefix,
			'menu' => $this->menuItem,
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, SocialLink $socialLink, SocialLinkRepository $repository): Response
	{
		$form = $this->createForm(SocialLinkType::class, $socialLink);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$repository->add($socialLink, true);

			$this->addFlash('success', 'element.updated');
			return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('admin/social_links/edit.html.twig', [
			'socialLink' => $socialLink,
			'form' => $form,
			'prefix' => $this->routePrefix,
		]);
	}

	#[Route('/{id}/delete', name: 'delete', methods: ['POST', 'DELETE'])]
	public function delete(Request $request, SocialLink $socialLink, SocialLinkRepository $repository): Response
	{
		if ($request->isXmlHttpRequest()) {
			$request->request->add(json_decode($request->getContent(), true));
			if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
				return $this->json(['title' => 'invalid_csrf_token', 'detail' => 'Invalid Token'], Response::HTTP_FORBIDDEN);
			}
			$repository->remove($socialLink, true);
			return new JsonResponse([], 200);
		}

		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			$this->addFlash('error', 'invalid_csrf_token');
			return $this->redirectBack($this->routePrefix . '_index', []);
		}

		$repository->remove($socialLink, true);
		$this->addFlash('success', t('category.deleted'));
		return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
	}

	#[Route('/socials_links/configure', name: 'client')]
	public function socials(Request $request, SocialLinkRepository $repository): Response|JsonResponse
	{
		$form = $this->createForm(SocialLinkConfType::class);
		$form->handleRequest($request);
		$formInputs = $form->createView()->children;

		if ($form->isSubmitted()) {
			$socialLinks = $repository->findAll();
			foreach($socialLinks as $link) {
				$link->setUrl($form->get($link->getName())->getData());
				$this->em->persist($link);
			}
			$this->em->flush();
			if ($request->isXmlHttpRequest()){
				return new JsonResponse();
			}
			$this->addFlash('success', t('social_links.updated'));
		}


		unset($formInputs['_token']);
		return $this->renderForm('admin/social_links/configure.twig', [
			'form' => $form,
			'formInputs' => $formInputs,
			'menu' => 'social_client',
		]);
	}
}
