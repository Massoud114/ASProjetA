<?php

namespace App\Infrastructure\Service;

use App\Application\Purchase\Entity\Cart;
use App\Application\Purchase\Entity\CartItem;
use App\Application\Product\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Application\Product\Repository\ColorRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartStorage
{
    private SessionInterface $session;
    private ProductRepository $productRepository;
    private ColorRepository $colorRepository;

	public function __construct(private readonly RequestStack $requestStack, ProductRepository $productRepository, ColorRepository  $colorRepository)
    {
        $this->productRepository = $productRepository;
        $this->colorRepository = $colorRepository;

    }

    public function getOrCreateCart(): Cart
    {
        return $this->getCart() ?: new Cart();
    }

    public function getCart(): ?Cart
    {
	    $this->session = $this->requestStack->getSession();

        $key = self::getKey();
        if (!$this->session->has($key)) {
            return null;
        }
        $cart = $this->session->get($key);

        if (!$cart instanceof Cart) {
            throw new \InvalidArgumentException('Wrong cart type');
        }

        // create new so if we modify it, but don't want to save back, it's
        // not automatically modified in the session
        $newCart = new Cart();
        // refresh the Products from the database
        foreach ($cart->getItems() as $item) {
            $newCart->addItem(new CartItem(
                $this->productRepository->find($item->getProduct()),
                $item->getQuantity(),
                $item->getColor() ? $this->colorRepository->find($item->getColor()) : null
            ));
        }

        return $newCart;
    }

    private static function getKey(): string
    {
        return sprintf('_cart_storage');
    }

    public function save(Cart $cart)
    {
        $this->session->set(self::getKey(), $cart);
    }

    public function clearCart(): void
    {
        $this->session = $this->requestStack->getSession();
        $this->session->remove(self::getKey());
    }
}
