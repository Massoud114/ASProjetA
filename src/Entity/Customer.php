<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $firstname = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $lastname = null;

	#[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
	private array $pronoun = [];

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $phoneNumber = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $email = null;

	#[ORM\Column(type: Types::DATE_IMMUTABLE)]
	private ?\DateTimeImmutable $created_at = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $country = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $city = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $more = null;

	#[ORM\OneToMany(mappedBy: 'customer', targetEntity: Purchase::class)]
	private Collection $purchases;

	public function __construct()
	{
		$this->created_at = new \DateTimeImmutable();
		$this->purchases = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getFirstname(): ?string
	{
		return $this->firstname;
	}

	public function setFirstname(string $firstname): self
	{
		$this->firstname = $firstname;

		return $this;
	}

	public function getLastname(): ?string
	{
		return $this->lastname;
	}

	public function setLastname(?string $lastname): self
	{
		$this->lastname = $lastname;

		return $this;
	}

	public function getPronoun(): array
	{
		return $this->pronoun;
	}

	public function setPronoun(?array $pronoun): self
	{
		$this->pronoun = $pronoun;

		return $this;
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

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): self
	{
		$this->email = $email;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeImmutable
	{
		return $this->created_at;
	}

	public function setCreatedAt(\DateTimeImmutable $created_at): self
	{
		$this->created_at = $created_at;

		return $this;
	}

	public function getCountry(): ?string
	{
		return $this->country;
	}

	public function setCountry(?string $country): self
	{
		$this->country = $country;

		return $this;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function setCity(?string $city): self
	{
		$this->city = $city;

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

	public function __toString(): string
	{
		return $this->firstname . (' ' . $this->lastname ?? null);
	}

	/**
	 * @return Collection<int, Purchase>
	 */
	public function getPurchases(): Collection
	{
		return $this->purchases;
	}

	public function addPurchase(Purchase $purchase): self
	{
		if (!$this->purchases->contains($purchase)) {
			$this->purchases->add($purchase);
			$purchase->setCustomer($this);
		}

		return $this;
	}

	public function removePurchase(Purchase $purchase): self
	{
		if ($this->purchases->removeElement($purchase)) {
			// set the owning side to null (unless already changed)
			if ($purchase->getCustomer() === $this) {
				$purchase->setCustomer(null);
			}
		}

		return $this;
	}
}
