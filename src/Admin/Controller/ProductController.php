<?php

namespace App\Admin\Controller;

use App\Application\Product\Product;
use App\Application\Media\Image\Image;
use Doctrine\ORM\EntityManagerInterface;
use App\Helper\Paginator\PaginatorInterface;
use App\Infrastructure\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Product\Form\ProductType;
use App\Application\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use App\Application\Media\Image\ImageRepository;
use App\Application\Purchase\PurchaseRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/product', name: 'product_')]
//#[IsGranted('ROLE_STOCK')]
class ProductController extends CrudController
{
	protected string $menuItem = 'product';
	protected string $routePrefix = 'admin_product';
	private PaginatorInterface $paginator;

	public function __construct(
		private readonly EntityManagerInterface $em,
		PaginatorInterface                      $paginator,
		private readonly CacheManager           $cacheManager,
		private readonly UrlGeneratorInterface  $urlGenerator,
	){
		$this->paginator = $paginator;
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index(Request $request, ProductRepository $repository): Response
	{
		$query = $repository
			->createQueryBuilder('row')
		    ->orderBy('row.createdAt', "DESC");

		if ($request->get('q')) {
			$query = $this->applySearch(trim($request->get('q')), $query, ['name', 'fixedPrice']);
		}

//		$this->paginator

		$this->paginator->allowSort('row.id', 'row.title', 'row.fixedPrice', 'row.createdAt');
		$rows = $this->paginator->paginate($query->getQuery());

		$template = $request->isXmlHttpRequest() ? '_list' : 'index';

		return $this->render("admin/product/{$template}.html.twig", [
			'rows' => $rows,
			'searchable' => true,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
			'thumbnailPath' => $this->getParameter('product_thumbnail_dir'),
		]);
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function create(Request $request, FileUploader $fileUploader, SluggerInterface $slugger): Response
	{
		$product = new Product();

		$form = $this->createForm(ProductType::class, $product)
		             ->add('saveAndCreateNew', SubmitType::class);

		$form->handleRequest($request);

		if ($form->isSubmitted() and $form->isValid()) {

			if ($form->has('thumbnailUrl')) {
				$thumbnailFile = $form->get('thumbnailUrl')->getData();
				if ($thumbnailFile instanceof UploadedFile) {
					$this->persistImage($thumbnailFile, $product, $fileUploader);
					$product->addProductImage($product->getThumbnail());
				}
			}

			$product->setSlug($slugger->slug($product->getName()));
			$this->removeUnit($product);
			$this->em->persist($product);

			$this->em->flush();
			$this->addFlash('success', 'product.created');

			if ($form->get('saveAndCreateNew')->isClicked()) {
				return $this->redirectToRoute($this->routePrefix . '_create');
			}
			return $this->redirectToRoute($this->routePrefix . '_index');
		}

		return $this->renderForm("admin/product/create.html.twig", [
			'form' => $form,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
			'product' => $product,
		]);

	}

	/**
	 * @throws \Exception
	 */
	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Product $product, FileUploader $fileUploader, SluggerInterface $slugger): Response
	{
		$form = $this->createForm(ProductType::class, $product);
		$form->handleRequest($request);

		if ($product->getThumbnail()) {
			$product->setThumbnailUrl(
		        new File($this->getParameter('product_thumbnail_dir') . '/' . $product->getThumbnail()->getName())
			);
		}

		if ($form->isSubmitted() && $form->isValid()){

			if ($form->has('thumbnailUrl')) {
				$thumbnailFile = $form->get('thumbnailUrl')->getData();
				if ($thumbnailFile instanceof UploadedFile) {
					$this->persistImage($thumbnailFile, $product, $fileUploader);
					$product->addProductImage($product->getThumbnail());
				}
			}

			$product->setSlug($slugger->slug($product->getName()));
			$this->em->flush();

			$this->addFlash('success', 'product.updated');

			return $this->redirectToRoute($this->routePrefix . '_index');
		}
		return $this->renderForm('admin/product/edit.html.twig', [
			'form' => $form,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
			'product' => $product
		]);
	}

	#[Route('/{id}/delete', name: 'delete' , methods: ['POST'])]
	public function delete(Product $product, Request $request): Response
	{
		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			$this->addFlash('error', 'invalid_csrf_token');
			$this->back();
		}

		if ($product->isPurchased() and !$request->request->get('force', false)) {
			$this->addFlash('error', 'product.cannot_delete');
			$this->back();
		}

		$product->getProductImages()->clear();
//		$product->getPurchases()->clear();

		$this->em->remove($product);
		$this->em->flush();

		$this->addFlash('success', 'product.deleted_successfully');

		return $this->redirect($this->getRedirectUrl($request, $this->urlGenerator));
	}

	#[Route('/{id}/show', name: 'show')]
	public function show(Product $product): Response
	{
		return $this->crudShow($product);
	}

	#[Route('/massive-delete', name: 'massive_delete')]
	public function massiveDelete(Request $request, ImageRepository $imageRepository, PurchaseRepository $purchaseRepository, ProductRepository $productRepository): Response
	{
		if (!$this->isCsrfTokenValid('massive-delete', $request->request->get('token'))) {
			$this->addFlash('error', 'invalid_csrf_token');
			$this->back();
		}

		$ids = $request->request->get('productIds');
		$ids = explode(',', $ids);

		/*if ($purchaseRepository->countPurchaseByProducts($ids) > 0) {
			$this->addFlash('error', 'product.cannot_delete');
			$this->back();
		}*/
		$imageRepository->removeByProducts($ids);
		$productRepository->removeByIds($ids);

		$this->addFlash("success", "products.delete.successfully");

		return $this->redirect($this->getRedirectUrl($request, $this->urlGenerator));
	}



	/**
	 * @throws \Exception
	 */
	private function persistImage(UploadedFile $file, Product $product, FileUploader $fileUploader)
	{
		$fileSize = $file->getSize();
		$filename = $fileUploader->upload($file);

		$image = new Image();
		$image->setSize($fileSize);
		$image->setName($filename);

		$product->setThumbnailUrl($filename);
		$product->setThumbnail($image);
		$image->setProduct($product);

		$this->cacheManager->getBrowserPath($this->getParameter('product_thumbnail_dir') . '/' . $image->getName(), 'thumbnail');

		$this->em->persist($image);
	}

	private function removeUnit(Product $product) : void
	{
		$units = ['weight', 'length', 'width', 'height', 'thickness'];
		foreach ($units as $unit) {
			$getMethod = 'get' . ucfirst($unit);
			if (!$product->$getMethod()) {
				$method = 'set' . ucfirst($unit) . 'Unit';
				$product->$method(null);
			}
		}
	}
}
