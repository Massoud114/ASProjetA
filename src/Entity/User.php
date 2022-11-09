<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

	#[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
	private array $pronoun = [];

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $phoneNumber = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
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
