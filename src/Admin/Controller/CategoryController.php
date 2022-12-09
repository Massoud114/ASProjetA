<?php

namespace App\Admin\Controller;

use App\Application\Product\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Product\Form\CategoryType;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Product\Repository\CategoryRepository;

#[Route('/category', name: 'category_')]
class CategoryController extends CrudController
{
	protected string $menuItem = 'category';
	protected string $routePrefix = 'admin_category';

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->getCategories(),
	        'prefix' => $this->routePrefix,
	        'menu' => $this->menuItem
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

            return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/create.html.twig', [
            'category' => $category,
            'form' => $form,
	        'prefix' => $this->routePrefix,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
	    $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

            return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
	        'prefix' => $this->routePrefix,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        } else {
	        $this->addFlash('error', 'invalid_csrf_token');
	        return $this->redirectToRoute($this->routePrefix . '_index');
        }

	    return $this->redirectToRoute($this->routePrefix . '_index', [], Response::HTTP_SEE_OTHER);

    }
}
