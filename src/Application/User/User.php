<?php

namespace App\Application\User;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Application\Purchase\Purchase;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use App\Infrastructure\Auth\Entity\SocialLoggableTrait;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use function Symfony\Component\Translation\t;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

	use SocialLoggableTrait;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

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
               	private ?DateTimeImmutable $created_at = null;

	#[ORM\Column(length: 2, nullable: true, options: ['default' => 'BJ'])]
               	private ?string $country = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
               	private ?string $more = null;

	#[ORM\OneToMany(mappedBy: 'customer', targetEntity: Purchase::class)]
               	private Collection $purchases;

	#[ORM\Column(type: Types::STRING, nullable: true, options: ['default' => 'null'])]
               	private ?string $theme = null;

	#[Vich\UploadableField(mapping:"avatars", fileNameProperty:"avatarName")]
               	private ?File $avatarFile = null;

	#[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
               	private ?string $avatarName = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
               	private ?\DateTimeInterface $updatedAt = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
               	private ?\DateTimeInterface $bannedAt = null;

	#[ORM\Column(type: Types::STRING, nullable: true, options: ['default' => null])]
               	private ?string $lastLoginIp = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
               	private ?\DateTimeInterface $lastLoginAt = null;

	#[ORM\Column(type: Types::STRING, nullable: true, options: ['default' => null])]
               	private ?string $invoiceInfo = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['default' => null])]
               	private DateTime $agreedTermsAt;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

	public function __construct() {
               		$this->created_at = new DateTimeImmutable();
               	}

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

	public function getTheme(): ?string
               	{
               		return $this->theme;
               	}

	public function setTheme(?string $theme): self
               	{
               		$this->theme = $theme;
               
               		return $this;
               	}

	public function getPronoun(): array
               	{
               		return $this->pronoun;
               	}

	public function setPronoun(array $pronoun): User
               	{
               		$this->pronoun = $pronoun;
               		return $this;
               	}

	public function getPhoneNumber(): ?string
               	{
               		return $this->phoneNumber;
               	}

	public function setPhoneNumber(?string $phoneNumber): User
               	{
               		$this->phoneNumber = $phoneNumber;
               		return $this;
               	}

	public function getCreatedAt(): ?DateTimeImmutable
               	{
               		return $this->created_at;
               	}

	public function setCreatedAt(?DateTimeImmutable $created_at): User
               	{
               		$this->created_at = $created_at;
               		return $this;
               	}

	public function getCountry(): ?string
               	{
               		return $this->country;
               	}

	public function setCountry(?string $country): User
               	{
               		$this->country = $country;
               		return $this;
               	}

	public function getMore(): ?string
               	{
               		return $this->more;
               	}

	public function setMore(?string $more): User
               	{
               		$this->more = $more;
               		return $this;
               	}

	public function getAvatarFile(): ?File
               	{
               		return $this->avatarFile;
               	}

	public function setAvatarFile(?File $avatarFile): User
               	{
               		$this->avatarFile = $avatarFile;
               		return $this;
               	}

	public function getAvatarName(): ?string
               	{
               		return $this->avatarName;
               	}

	public function setAvatarName(?string $avatarName): User
               	{
               		$this->avatarName = $avatarName;
               		return $this;
               	}

	public function getUpdatedAt(): ?\DateTimeInterface
               	{
               		return $this->updatedAt;
               	}

	public function setUpdatedAt(?\DateTimeInterface $updatedAt): User
               	{
               		$this->updatedAt = $updatedAt;
               		return $this;
               	}

	public function getBannedAt(): ?\DateTimeInterface
               	{
               		return $this->bannedAt;
               	}

	public function setBannedAt(?\DateTimeInterface $bannedAt): User
               	{
               		$this->bannedAt = $bannedAt;
               		return $this;
               	}

	public function isBanned(): bool
               	{
               		return null !== $this->bannedAt;
               	}

	public function canLogin(): bool
               	{
               		return !$this->isBanned();
               	}

	public function getLastLoginIp(): ?string
               	{
               		return $this->lastLoginIp;
               	}

	public function setLastLoginIp(?string $lastLoginIp): User
               	{
               		$this->lastLoginIp = $lastLoginIp;
               		return $this;
               	}

	public function getLastLoginAt(): ?\DateTimeInterface
               	{
               		return $this->lastLoginAt;
               	}

	public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): User
               	{
               		$this->lastLoginAt = $lastLoginAt;
               		return $this;
               	}

	public function getInvoiceInfo(): ?string
               	{
               		return $this->invoiceInfo;
               	}

	public function setInvoiceInfo(?string $invoiceInfo): User
               	{
               		$this->invoiceInfo = $invoiceInfo;
               		return $this;
               	}

	public function getAgreedTermsAt(): ?DateTimeInterface
               	{
               		return $this->agreedTermsAt;
               	}

	public function agreeToTerms()
               	{
               		$this->agreedTermsAt = new DateTime();
               	}

	public function getRoleIdentifier(): string
               	{
               		if (in_array('ROLE_SUPER_ADMIN', $this->roles)) {
               			return t('super_admin');
               		} else if (in_array('ROLE_DEVELOPER', $this->roles)) {
               			return t('developer');
               		} else if (in_array('ROLE_STOCK', $this->roles)) {
               			return t('administrator');
               		}else if (in_array('ROLE_MANAGER', $this->roles)) {
               			return t('manager');
               		} else {
               			throw new AccessDeniedException('Vous n\'avez pas les droits pour accéder à cette page');
               		}
               	}

	public function getInitials(): string
               	{
               		return strtoupper(substr($this->firstname, 0, 1) . substr($this->lastname, 0, 1));
               	}

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
