<?php

namespace App\Client\Controller\Shop;


use App\Application\Product\Product;
use App\Application\Product\Entity\Color;
use App\Infrastructure\Service\CartStorage;
use Symfony\Component\HttpFoundation\Request;
use App\Application\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Application\Product\Repository\ColorRepository;
use App\Application\Purchase\Form\AddItemToCartFormType;
use App\Application\Product\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use function Symfony\Component\Translation\t;

class CartController extends AbstractController
{

	#[Route(path: '/panier', name: 'app_cart')]
    public function shoppingCart(Request $request, CartStorage $cartStorage, ProductRepository  $productRepository): Response
    {
		$cart = $cartStorage->getOrCreateCart();

        /*$featuredProduct = $productRepository->findFeatured();
        $addToCartForm = $this->createForm(AddItemToCartFormType::class, null, [
            'product' => $featuredProduct,
        ]);*/

        return $this->renderForm('shop/cart/cart.html.twig', [
            'cart' => $cart,
//            'featuredProduct' => $featuredProduct,
//            'addToCartForm' => $addToCartForm->createView()
        ]);
    }

	#[Route(path : '/product/{slug}', name: 'app_product_show', methods: ['GET', 'POST'])]
    public function addItemToCart(
		Product $product,
		Request $request,
		CategoryRepository $categoryRepository,
		CartStorage $cartStorage
	): RedirectResponse | Response
	{
        $addToCartForm = $this->createForm(AddItemToCartFormType::class, null, [
            'product' => $product
        ]);

        $addToCartForm->handleRequest($request);
        if ($addToCartForm->isSubmitted() && $addToCartForm->isValid()) {
            $cart = $cartStorage->getOrCreateCart();
            $cart->addItem($addToCartForm->getData());
            $cartStorage->save($cart);

            $this->addFlash('success', t('item_added'));

            return $this->redirectToRoute('app_product_show', [
                'slug' => $product->getSlug(),
            ]);
        }

        return $this->renderForm('shop/product/show.html.twig', [
            'product' => $product,
            'categories' => $categoryRepository->findAll(),
            'addToCartForm' => $addToCartForm,
	        'cart' => $cartStorage->getOrCreateCart(),
        ]);
    }

	#[Route(path : '/panier/update/{productId}/{colorId?}', name : 'app_cart_update_item', methods : ['POST'])]
	public function updateItemToCart(
		$productId,
		$colorId,
		Request $request,
		CartStorage $cartStorage,
		ProductRepository $productRepository,
		ColorRepository $colorRepository
	): Response
	{
		/** @var Product|null $product */
		$product = $productRepository->find($productId);
		/** @var Color|null $color */
		$color = $colorId ? $colorRepository->find($colorId) : null;

		if (!$product) {
			throw $this->createNotFoundException();
		}

		if (!$this->isCsrfTokenValid('update_item', $request->request->get('_token'))) {
			throw new BadRequestHttpException('Invalid CSRF token');
		}

		$cart = $cartStorage->getOrCreateCart();
		$cartItem = $cart->findItem($product, $color);
		if ($cartItem) {
			$cartItem->setQuantity($request->request->getInt('quantity'));
		}
		$cartStorage->save($cart);

		$this->addFlash('success', 'Item removed!');

		if($request->isXmlHttpRequest()) {
			return $this->render('shop/cart/_item.html.twig', [
				'item' => $cartItem,
			]);
		}

		return $this->redirectToRoute('app_cart');
	}

	#[Route(
		path: '/panier/summary',
		name: 'app_cart_summary',
		methods: ['GET'],
		condition: 'request.isXmlHttpRequest()'
	)]
	public function cartSummary(CartStorage $cartStorage): Response
	{
		return $this->render('shop/cart/cart_summary.html.twig', [
			'cart' => $cartStorage->getOrCreateCart(),
		]);
	}

	#[Route(path : '/panier/remove/{productId}/{colorId?}', name : 'app_cart_remove_item', methods : ['POST'])]
    public function removeItemToCart($productId, $colorId, Request $request, CartStorage $cartStorage, ProductRepository $productRepository, ColorRepository $colorRepository): RedirectResponse
	{
        /** @var Product|null $product */
        $product = $productRepository->find($productId);
        /** @var Color|null $color */
        $color = $colorId ? $colorRepository->find($colorId) : null;

        if (!$product) {
            $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('remove_item', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $cart = $cartStorage->getOrCreateCart();
        $cartItem = $cart->findItem($product, $color);
        if ($cartItem) {
            $cart->removeItem($cartItem);
        }
        $cartStorage->save($cart);

        $this->addFlash('success', 'Item removed!');

        return $this->redirectToRoute('app_cart');
    }

	#[Route(path: '/panier/_list', name : '_app_cart_list')]
	public function _shoppingCartList(CartStorage $cartStorage): Response
	{
		return $this->render('shop/cart/_cartList.html.twig', [
			'cart' => $cartStorage->getOrCreateCart()
		]);
	}
}
