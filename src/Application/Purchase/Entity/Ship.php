<?php

namespace App\Application\Purchase\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Application\Purchase\Purchase;
use App\Application\Purchase\Repository\ShipRepository;

#[ORM\Entity(repositoryClass: ShipRepository::class)]
class Ship
{
	const STATES = [
		1 => "shipped",
		2 => "canceled",
	];

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\OneToOne(inversedBy: 'ship', cascade: ['persist', 'remove'])]
	#[ORM\JoinColumn(nullable: false)]
	private Purchase $purchase;

	#[ORM\Column(nullable: true)]
	private ?\DateTimeImmutable $shippedAt = null;
	#[ORM\Column]
	private int $state = 1;

	#[ORM\Column(nullable: true)]
	private ?int $price = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
	private ?\DateTimeInterface $shipping_estimated_date = null;

	#[ORM\Column(nullable: true)]
	private ?int $shippingTime = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $trackingNumber = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $shipper = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getPurchase(): ?Purchase
	{
		return $this->purchase;
	}

	public function setPurchase(Purchase $purchase): self
	{
		$this->purchase = $purchase;

		return $this;
	}

	public function getShippedAt(): ?\DateTimeImmutable
	{
		return $this->shippedAt;
	}

	public function setShippedAt(?\DateTimeImmutable $shippedAt): self
	{
		$this->shippedAt = $shippedAt;

		return $this;
	}

	public function getState(): ?int
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

	public function getPrice(): ?int
	{
		return $this->price;
	}

	public function setPrice(?int $price): self
	{
		$this->price = $price;

		return $this;
	}

	public function getShippingEstimatedDate(): ?\DateTimeInterface
	{
		return $this->shipping_estimated_date;
	}

	public function setShippingEstimatedDate(?\DateTimeInterface $shipping_estimated_date): self
	{
		$this->shipping_estimated_date = $shipping_estimated_date;

		return $this;
	}

	public function getShippingTime(): ?int
	{
		return $this->shippingTime;
	}

	public function setShippingTime(?int $shippingTime): self
	{
		$this->shippingTime = $shippingTime;

		return $this;
	}

	public function getTrackingNumber(): ?string
	{
		return $this->trackingNumber;
	}

	public function setTrackingNumber(?string $trackingNumber): Ship
	{
		$this->trackingNumber = $trackingNumber;
		return $this;
	}

	public function getShipper(): ?string
	{
		return $this->shipper;
	}
	public function setShipper(?string $shipper): Ship
	{
		$this->shipper = $shipper;
		return $this;
	}

}
