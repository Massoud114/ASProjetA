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
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/product', name: 'product_')]
class ProductController extends CrudController
{
	protected string $menuItem = 'product';
	protected string $routePrefix = 'admin_product';
	private PaginatorInterface $paginator;

	public function __construct(
		private readonly EntityManagerInterface $em,
		PaginatorInterface $paginator,
		private readonly string $targetDirectory,
		private readonly CacheManager $cacheManager,
	){
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
//		$this->paginator->

		$this->paginator->allowSort('row.id', 'row.title', 'row.fixedPrice', 'row.createdAt');
		$rows = $this->paginator->paginate($query->getQuery());

		return $this->render("admin/product/index.html.twig", [
			'rows' => $rows,
			'searchable' => true,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
		]);
	}

	/**
	 * @throws \Exception
	 */
	#[Route('/create', name: 'create')]
	public function create(Request $request, FileUploader $fileUploader, SluggerInterface $slugger): Response
	{
		$product = new Product();

		$form = $this->createForm(ProductType::class, $product);
		$form->handleRequest($request);

		if ($form->isSubmitted() and $form->isValid()) {
			$thumbnailFile = $form->get('thumbnail')->getData();

			if ($thumbnailFile instanceof UploadedFile) {
				$filename = $fileUploader->upload($thumbnailFile);

				$image = new Image();
				$image->setSize($thumbnailFile->getSize());
				$image->setName($filename);

				$product->setThumbnailUrl($this->targetDirectory . '/' . $filename);
				$product->setThumbnail($image);
				$image->setProduct($product);
				$this->em->persist($image);
			}

			$product->setSlug($slugger->slug($product->getName()));
			$this->em->persist($product);

			$this->em->flush();
			$this->addFlash('success', 'Product created successfully');
			return $this->redirectToRoute('admin_product_index');
		}

		return $this->renderForm("admin/product/create.html.twig", [
			'form' => $form,
			'menu' => $this->menuItem,
			'prefix' => $this->routePrefix,
		]);


	}

	#[Route('/{id}/edit', name: 'edit')]
	public function edit(Product $product): Response
	{
		//$product->setBrochureFilename(
		//    new File($this->getParameter('brochures_directory').'/'.$product->getBrochureFilename())
		//);
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

	private function handleThumbnail(Image $image) : void
	{
		// Liip thumbnail the image
		$this->cacheManager->getBrowserPath($this->targetDirectory . '/' . $image->getName(), 'thumbnail');
	}
}
