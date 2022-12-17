<?php

namespace App\Client\Controller\Shop;


use App\Application\Purchase\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use App\Infrastructure\Service\CartStorage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Application\Product\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Purchase\PurchaseRepository;
use App\Application\Purchase\Form\CheckoutFormType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CheckoutController extends AbstractController
{
	public function __construct(private readonly EntityManagerInterface $entityManager) { }

	#[Route(path: "/checkout", name: "app_checkout")]
    public function checkout(
		ProductRepository $productRepository,
		Request $request,
		CartStorage $cartStorage,
		EntityManagerInterface $entityManager,
		SessionInterface $session
	): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

		$cart = $cartStorage->getCart();
		if(!$cart){
			$this->addFlash("warning", "cart.empty");
			return $this->redirectToRoute('app_cart');
		}

        $checkoutForm = $this->createForm(CheckoutFormType::class);
        $featuredProduct = $productRepository->findFeatured();
        /*$addToCartForm = $this->createForm(AddItemToCartFormType::class, null, [
            'product' => $featuredProduct,
        ]);*/

        $checkoutForm->handleRequest($request);
        if ($checkoutForm->isSubmitted() && $checkoutForm->isValid()) {
            /** @var Purchase $purchase */
            $purchase = $checkoutForm->getData();
			$purchase
				->setCustomer($this->getUser())
				->setOrderNumber($this->generateOrderNumber())
				->setConfirmed(false)
			;
            $purchase->addItemsFromCart($cartStorage->getCart());

            $entityManager->persist($purchase);
            $entityManager->flush();

            $session->set('purchase_id', $purchase->getId());
            $cartStorage->clearCart();

            return $this->redirectToRoute('app_confirmation');
        }

        return $this->render('shop/checkout/checkout.html.twig', [
            'checkoutForm' => $checkoutForm->createView(),
            'featuredProduct' => $featuredProduct,
	        'cart' => $cart,
        ]);
    }

	private function generateOrderNumber()
	{
		return $this->entityManager->getRepository(Purchase::class)->getLatestOrderNumber() + 1;
	}

    /**
     * @Route("/confirmation", name="app_confirmation")
     */
    public function confirmation(SessionInterface $session, PurchaseRepository $purchaseRepository): Response
    {
        $purchaseId = $session->get('purchase_id');
		if(!$purchaseId) {
			$this->addFlash('warning', 'cart.empty');
			return $this->redirectToRoute('app_cart');
		}
        $purchase = $purchaseRepository->find($purchaseId);

        if (!$purchase) {
            throw $this->createNotFoundException('No purchase found');
        }

        return $this->render('checkout/confirmation.html.twig', [
            'purchase' => $purchase,
        ]);
    }
}
