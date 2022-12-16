<?php

namespace App\Application\Purchase\Entity;

use App\Application\Product\Product;
use App\Application\Product\Entity\Color;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Assert\Callback("validateColor")
 */
class CartItem
{
    /**
     * @var Product
     */
    private Product $product;

    /**
     * @var Color|null
     */
    private ?Color $color;

    /**
     * @Assert\GreaterThanOrEqual(1, message="Enter a quantity greater than 0")
     * @Assert\NotBlank(message="Please enter a valid quantity")
     * @var int
     */
    private int $quantity = 1;

    public function __construct(Product $product, int $quantity = 1, Color $color = null)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->color = $color;
    }

    public function increaseQuantity(int $quantity): self
    {
        $this->quantity += $quantity;
	    return $this;
    }

    public function matches(CartItem $cartItem): bool
    {
        $thisKey = sprintf('%s_%s', $this->getProduct()->getId(), $this->getColor() ? $this->getColor()->getId() : 'no_color');
        $thatKey = sprintf('%s_%s', $cartItem->getProduct()->getId(), $cartItem->getColor() ? $cartItem->getColor()->getId() : 'no_color');

        return $thisKey === $thatKey;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(Color $color): self
    {
        $this->color = $color;
		return $this;
    }

    public function validateColor(ExecutionContextInterface $context): void
    {
        if (!$this->product->hasColors()) {
            return;
        }

        if (!$this->color) {
            $context
                ->buildViolation('Please select a color')
                ->atPath('color')
                ->addViolation();
        }
    }

    public function getTotalString(): string
    {
        return (string) ($this->getTotal() / 100);
    }

    public function getTotal(): int
    {
        return $this->getProduct()->getPrice() * $this->getQuantity();
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;
	    return $this;
    }

    public function createPurchaseItem(): PurchaseProduct
    {
        $purchaseItem = new PurchaseProduct();
        $purchaseItem->setProduct($this->product);
        $purchaseItem->setQuantity($this->quantity);
        $purchaseItem->setColor($this->color);

        return $purchaseItem;
    }

    public function getIdentifier(): string
    {
        return sprintf('%s_%s',
            $this->product->getId(),
            $this->color ? $this->color->getId() : ''
        );
    }
}
