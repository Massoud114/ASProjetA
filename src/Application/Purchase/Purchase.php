<?php

namespace App\Application\Purchase;

use Doctrine\DBAL\Types\Types;
use App\Application\User\User;
use Doctrine\ORM\Mapping as ORM;
use App\Application\Invoice\Invoice;
use App\Application\Purchase\Entity\Ship;
use App\Application\Purchase\Entity\Cart;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Application\Purchase\Entity\PurchaseProduct;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use function Symfony\Component\Translation\t;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[UniqueEntity(fields: ['orderNumber'], message: 'There is already an order with different number')]
class Purchase
{
	const STATES = [
		1 => 'progress',
		2 => 'done',
		3 => 'canceled',
		4 => 'rejected'
	];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

	#[ORM\Column(type: Types::INTEGER, nullable: true)]
	private ?int $orderNumber = 1000;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    private int $status = 1;

    #[
		ORM\ManyToOne(targetEntity: User::class, inversedBy: 'purchases'),
	    ORM\JoinColumn(nullable: false),
    ]
    private ?User $customer = null;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: PurchaseProduct::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $purchaseProducts;

    #[ORM\OneToOne(mappedBy: 'purchase', cascade: ['persist', 'remove'])]
    private Ship $ship;

    #[ORM\OneToOne(mappedBy: 'purchase', cascade: ['persist', 'remove'])]
    private Invoice $invoice;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $more = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
	private ?\DateTime $estimatedMakeAt = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	#[Assert\Regex(pattern: '/^[0-9]{8}$/')]
	private ?string $phoneNumber = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $notes = null;

	#[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
	private ?bool $confirmed = false;

    public function __construct()
    {
	    $this->createdAt = new \DateTimeImmutable();
	    $this->purchaseProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

	public function getOrderNumber(): ?int
	{
		return $this->orderNumber;
	}

	public function setOrderNumber(?int $orderNumber): self
	{
		if ($orderNumber < 1000){
			throw new \RuntimeException('Please fill a value greater than 900');
		}
		$this->orderNumber = $orderNumber;
		return $this;
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

    public function getStatusString(): string
    {
        return self::STATES[$this->status];
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
		if (!in_array($status, array_keys(self::STATES))){
			throw new \RuntimeException('Invalid status passed to Purchase');
		}
        $this->status = $status;
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
     * @return Collection|PurchaseProduct[]
     */
    public function getPurchaseProducts(): Collection | array
    {
        return $this->purchaseProducts;
    }

    public function addPurchaseProduct(PurchaseProduct $product): self
    {
        if (!$this->purchaseProducts->contains($product)) {
            $this->purchaseProducts->add($product);
            $product->setPurchase($this);
        }

        return $this;
    }

    public function removePurchaseProduct(PurchaseProduct $product): self
    {
        if ($this->purchaseProducts->removeElement($product)) {
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

	public function getEstimatedMakeAt(): ?\DateTime
	{
		return $this->estimatedMakeAt;
	}

	public function setEstimatedMakeAt(?\DateTime $estimatedMakeAt): Purchase
	{
		$this->estimatedMakeAt = $estimatedMakeAt;
		return $this;
	}

	public function addItemsFromCart(Cart $cart): void
	{
		foreach ($cart->getItems() as $item) {
			$this->addPurchaseProduct($item->createPurchaseItem());
		}
	}

	public function getTotal(): int
	{
		return array_reduce($this->purchaseProducts->toArray(), function($accumulator, PurchaseProduct $item) {
			return $accumulator + $item->getTotal();
		}, 0);
	}

	public function getPhoneNumber(): ?string
	{
		return $this->phoneNumber;
	}

	public function setPhoneNumber(?string $phoneNumber): self
	{
		$this->phoneNumber = $phoneNumber;
		return $this;
	}

	public function getNotes(): ?string
	{
		return $this->notes;
	}

	public function setNotes(?string $notes): self
	{
		$this->notes = $notes;
		return $this;
	}

	public function isConfirmed(): ?bool
	{
		return $this->confirmed;
	}

	public function setConfirmed(?bool $confirmed): self
	{
		$this->confirmed = $confirmed;
		return $this;
	}

	public function getShortLabel(): string
	{
		$nbProducts = $this->purchaseProducts->count();
		if ($nbProducts == 1) {
			return $this->purchaseProducts->first()->getProduct()->getName();
		}
		return $nbProducts . " " . t('products');
	}

	public function getStatusBadge(): string
	{
		switch ($this->status){
			case 1:
				if($this->confirmed)
					return "success"; // new
				return "warning"; // progress
			case 2:
				return "success"; // done
			case 3:
				return "secondary"; // canceled
			case 4:
				return "danger"; // rejected
		}
		return "";
	}
}
