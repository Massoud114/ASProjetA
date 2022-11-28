<?php

namespace App\Admin\Controller;

use App\Application\Settings\Faq\Faq;
use App\Application\Settings\Faq\FaqType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Settings\Faq\FaqRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

#[Route('/faq', name: 'faq_')]
class FaqController extends CrudController
{
	protected string $menuItem = 'faq';
	protected string $routePrefix = 'admin_faq';

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(FaqRepository $faqRepository): Response
    {
        return $this->render('admin/faq/index.html.twig', [
            'faqs' => $faqRepository->getAdmins(),
	        'prefix' => $this->routePrefix,
	        'menu' => $this->menuItem
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function new(Request $request, FaqRepository $faqRepository): Response
    {
        $faq = new Faq();
        $form = $this->createForm(FaqType::class, $faq)
	        ->add('destination', HiddenType::class, ['data' => $this->isGranted('ROLE_DEVELOPER') ? 'admin' : 'client']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faqRepository->add($faq, true);

            return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/faq/create.html.twig', [
            'faq' => $faq,
            'form' => $form,
	        'prefix' => $this->routePrefix,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Faq $faq, FaqRepository $faqRepository): Response
    {
        $form = $this->createForm(FaqType::class, $faq)
	        ->add('destination', HiddenType::class, ['data' => $this->isGranted('ROLE_DEVELOPER') ? 'admin' : 'client']);
	    $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faqRepository->add($faq, true);

            return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/faq/edit.html.twig', [
            'faq' => $faq,
            'form' => $form,
	        'prefix' => $this->routePrefix,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Faq $faq, FaqRepository $faqRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$faq->getId(), $request->request->get('_token'))) {
            $faqRepository->remove($faq, true);
        } else {
	        $this->addFlash('error', 'invalid_csrf_token');
	        return $this->redirectToRoute($this->routePrefix . '_index');
        }

	    return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);

    }
}
