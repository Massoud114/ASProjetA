<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PurchaseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
	const STATES = [
		1 => 'En cours',
		2 => 'Livrée',
		3 => 'Annulée'
	];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    private int $state = 1;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $customer = null;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: PurchaseProduct::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $products;

    #[ORM\OneToOne(mappedBy: 'purchase', cascade: ['persist', 'remove'])]
    private Ship $ship;

    #[ORM\OneToOne(mappedBy: 'purchase', cascade: ['persist', 'remove'])]
    private Invoice $invoice;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $more = null;

    public function __construct()
    {
	    $this->createdAt = new \DateTimeImmutable();
	    $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getState(): string
    {
        return self::STATES[$this->state];
    }

    public function setState(int $state): self
    {
        $this->state = $state;

			//TODO : Throw an error
		/*if (!in_array($state, array_keys(self::STATES))){
		}*/

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(?User $user): self
    {
        $this->customer = $user;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(PurchaseProduct $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setPurchase($this);
        }

        return $this;
    }

    public function removeProduct(PurchaseProduct $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getPurchase() === $this) {
                $product->setPurchase(null);
            }
        }

        return $this;
    }

    public function getShip(): Ship
    {
        return $this->ship;
    }

    public function setShip(Ship $ship): self
    {
        // set the owning side of the relation if necessary
        if ($ship->getPurchase() !== $this) {
            $ship->setPurchase($this);
        }

        $this->ship = $ship;

        return $this;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): self
    {
        // set the owning side of the relation if necessary
        if ($invoice->getPurchase() !== $this) {
            $invoice->setPurchase($this);
        }

        $this->invoice = $invoice;

        return $this;
    }

    public function getMore(): ?string
    {
        return $this->more;
    }

    public function setMore(?string $more): self
    {
        $this->more = $more;

        return $this;
    }

	public function getTotalString(): string
	{
		return (string) ($this->getTotal() / 100);
	}

	public function getTotal(): int
	{
		return array_reduce($this->products->toArray(), function($accumulator, PurchaseProduct $item) {
			return $accumulator + $item->getTotal();
		}, 0);
	}
}
