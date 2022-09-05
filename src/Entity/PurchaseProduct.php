<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PurchaseProductRepository;

#[ORM\Entity(repositoryClass: PurchaseProductRepository::class)]
class PurchaseProduct
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private Purchase $purchase;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\ManyToOne]
    private ?Color $color = null;

    #[ORM\Column]
    private int $quantity;

    #[ORM\Column]
    private int $price;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $size = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(?Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;
		$this->price = $product->getMinPrice();

        return $this;
    }

    public function getColor(): ?Color
    {
        return $this->color;
    }

    public function setColor(?Color $color): self
    {
        $this->color = $color;

        return $this;
    }

	public function getTotalString(): string
	{
		return (string) ($this->getTotal() / 100);
	}

	public function getTotal(): int
	{
		return $this->getPrice() * $this->getQuantity();
	}

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }
}
