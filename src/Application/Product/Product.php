<?php

namespace App\Application\Product;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Application\Campaign\Promotion;
use App\Application\Media\Image\Image;
use App\Application\Product\Entity\Category;
use App\Application\Purchase\Entity\PurchaseProduct;
use App\Helper\Entity\MesurableTrait;
use App\Helper\Entity\SluggableTrait;
use App\Helper\Entity\TimestampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'product.name.unique')]
#[ApiResource(
	operations: [
		new Get(
			normalizationContext: [
				'groups' => ['product:item:read'],
				'enable_max_depth' => false,
			],
			security: 'is_granted("ROLE_ADMIN")y',
		),
		new Patch(
			denormalizationContext: [
				'groups' => ['product:write'],
				'enable_max_depth' => false,
			],
			security: 'is_granted("ROLE_ADMIN")',
		),
		new Delete(),
	]
)]
class Product
{
	public const TYPES = [
		'single' => 'single',
		'pack' => 'pack',
		'on_command' => 'on_command',
	];

	public const WEIGHT_UNITS = [
		'kg' => 'kg',
		'g' => 'g',
		'mg' => 'mg',
	];

	public const THICKNESS_UNITS = [
		'mm' => 'mm',
		'cm' => 'cm',
		'm' => 'm',
	];

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[
		ORM\Column(length: 255),
		Assert\NotBlank,
		Assert\Length(min: 3, max: 255),
		Assert\Type(type: 'string'),
		Groups(['product:item:read', 'product:collection:read', 'product:write'])
	]
	private string $name;

	#[
		ORM\Column(type: Types::TEXT, nullable: true),
		Assert\Type(type: 'string'),
		Groups(['product:item:read', 'product:write'])
	]
	private ?string $description = null;

	#[
		ORM\Column(type: Types::STRING, nullable: true),
		Assert\Choice(choices: self::TYPES, message: 'product.types.invalid'),
		Groups(['product:item:read', 'product:write'])
	]
	private ?string $type = null;

	#[
		ORM\Column(nullable: true),
		Assert\Type(type: 'numeric'),
		Assert\PositiveOrZero,
		Groups(['product:item:read', 'product:write'])
	]
	private int $minPrice;

	#[
		ORM\Column(nullable: true),
		Assert\Type(type: 'numeric'),
		Assert\PositiveOrZero,
		Groups(['product:item:read', 'product:write'])
	]
	private ?int $maxPrice = null;

	#[
		ORM\Column,
		Assert\Type(type: 'numeric'),
		Assert\PositiveOrZero,
		Assert\NotBlank,
		Groups(['product:item:read', 'product:collection:read', 'product:write'])
	]
	private int $stockQuantity = 0;

	#[
		ORM\Column(nullable: true),
		Assert\Type(type: 'numeric'),
		Assert\PositiveOrZero,
		Assert\NotBlank,
		Groups(['product:item:read', 'product:write'])
	]
	private ?float $makingPrice = null;

	#[ORM\Column(nullable: true)]
	#[Assert\Type(type: 'numeric')]
	#[Assert\PositiveOrZero]
	#[Assert\NotBlank]
	#[Groups(['product:item:read', 'product:collection:read', 'product:write'])]
	private ?float $fixedPrice = 0;

	#[ORM\Column(nullable: false)]
	#[Groups(['product:item:read', 'product:collection:read'])]
	private ?string $thumbnailUrl = null;

	#[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
	#[Groups(['product:item:read', 'product:collection:read', 'product:write'])]
	private bool $visible = true;

	#[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
	#[Groups(['product:item:read', 'product:collection:read', 'product:write'])]
	private bool $favorite = false;

	#[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class, orphanRemoval: true)]
	#[Groups(['product:item:read', 'product:write'])]
	private Collection $productImages;

	#[ORM\OneToOne(cascade: ['persist', 'remove'])]
	#[ORM\JoinColumn(nullable: true)]
	#[Groups(['product:item:read', 'product:collection:read', 'product:write'])]
	private ?Image $thumbnail = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	#[Groups(['product:item:read', 'product:write'])]
	private ?string $details = null;

	#[ORM\OneToMany(mappedBy: 'product', targetEntity: PurchaseProduct::class, cascade: ['persist'], orphanRemoval: false)]
	private Collection $purchaseProducts;

	#[
		ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products'),
	]
	private Collection $categories;

	#[ORM\ManyToMany(targetEntity: Promotion::class, mappedBy: 'products')]
	private Collection $promotions;

	use MesurableTrait, SluggableTrait, TimestampTrait;

	public function __construct()
	{
		$this->createdAt = new \DateTimeImmutable();
		$this->updatedAt = new \DateTime();
		$this->productImages = new ArrayCollection();
		$this->categories = new ArrayCollection();
		$this->promotions = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(?string $type): self
	{
		if (!in_array($type, self::TYPES)) {
			throw new \RuntimeException('Invalid Type passed');
		}
		$this->type = $type;

		return $this;
	}

	public function getMinPrice(): int
	{
		return $this->minPrice;
	}

	public function setMinPrice(int $minPrice): self
	{
		$this->minPrice = $minPrice;

		return $this;
	}

	public function getMaxPrice(): ?string
	{
		return $this->maxPrice;
	}

	public function setMaxPrice(string $maxPrice): self
	{
		$this->maxPrice = $maxPrice;

		return $this;
	}

	public function getMaxPriceString(): string
	{
		return (string)$this->maxPrice / 100;
	}

	public function getStockQuantity(): int
	{
		return $this->stockQuantity;
	}

	public function setStockQuantity(int $stockQuantity): self
	{
		$this->stockQuantity = $stockQuantity;

		return $this;
	}

	public function isPurchased(): bool
	{
		return false;
	}

	public function getMakingPrice(): ?float
	{
		return $this->makingPrice;
	}

	public function setMakingPrice(?float $makingPrice): self
	{
		$this->makingPrice = $makingPrice;

		return $this;
	}

	public function __toString(): string
	{
		return sprintf('%s ($%s)', $this->name, $this->getFixedPriceString());
	}

	public function getFixedPriceString(): string
	{
		return (string)$this->fixedPrice / 100;
	}

	public function getThumbnailUrl(): ?string
	{
		return $this->thumbnailUrl;
	}

	public function setThumbnailUrl(?string $thumbnailUrl): self
	{
		$this->thumbnailUrl = $thumbnailUrl;

		return $this;
	}

	public function isVisible(): bool
	{
		return $this->visible;
	}

	public function setVisible(bool $visible = true): Product
	{
		$this->visible = $visible;
		return $this;
	}

	public function isFavorite(): bool
	{
		return $this->favorite;
	}

	public function setFavorite(bool $favorite = true): Product
	{
		$this->favorite = $favorite;
		return $this;
	}

	public function isAvailable(): bool
	{
		return $this->stockQuantity > 0;
	}

	/**
	 * @return Collection<int, Image>
	 */
	public function getProductImages(): Collection
	{
		return $this->productImages;
	}

	public function addProductImage(Image $productImage): self
	{
		if (!$this->productImages->contains($productImage)) {
			$this->productImages->add($productImage);
			$productImage->setProduct($this);
		}

		return $this;
	}

	public function removeProductImage(Image $productImage): self
	{
		if ($this->productImages->removeElement($productImage)) {
			// set the owning side to null (unless already changed)
			if ($productImage->getProduct() === $this) {
				$productImage->setProduct(null);
			}
		}

		return $this;
	}

	public function getThumbnail(): ?Image
	{
		return $this->thumbnail;
	}

	public function setThumbnail(Image $thumbnail): self
	{
		$this->thumbnail = $thumbnail;

		return $this;
	}

	public function getDetails(): ?string
	{
		return $this->details;
	}

	public function setDetails(?string $details): self
	{
		$this->details = $details;

		return $this;
	}

	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
	}

	public function getUpdatedAt(): \DateTime
	{
		return $this->updatedAt;
	}

	public function setCreatedAt(\DateTimeImmutable $createdAt): Product
	{
		$this->createdAt = $createdAt;
		return $this;
	}

	public function setUpdatedAt(\DateTime $updatedAt): Product
	{
		$this->updatedAt = $updatedAt;
		return $this;
	}

	/**
	 * @return Collection<int, Category>
	 */
	public function getCategories(): Collection
	{
		return $this->categories;
	}

	public function addCategory(Category $category): self
	{
		if (!$this->categories->contains($category)) {
			$this->categories->add($category);
		}

		return $this;
	}

	public function removeCategory(Category $category): self
	{
		$this->categories->removeElement($category);

		return $this;
	}

	public function isPromo(): bool
	{
		return false;
	}

	public function hasColors()
	{
		// TODO: Implement hasColors() method.
		return false;
	}

	public function sellCount(): int
	{
		return array_sum(array_map(function (PurchaseProduct $purchaseProduct) {
			return $purchaseProduct->getQuantity();
		}, $this->purchaseProducts->toArray()));
	}

	public function getEstimationAmount(): int
	{
		return $this->stockQuantity * $this->getPrice();
	}

	public function getPrice(): ?float
	{
		// TODO: Check if product is promo
		return $this->getFixedPrice();
	}

	public function getFixedPrice(): ?float
	{
		return $this->fixedPrice;
	}

	public function setFixedPrice(?float $fixedPrice): Product
	{
		$this->fixedPrice = $fixedPrice;
		return $this;
	}

	/**
	 * @return Collection<int, Promotion>
	 */
	public function getPromotions(): Collection
	{
		return $this->promotions;
	}

	public function addPromotion(Promotion $promotion): self
	{
		if (!$this->promotions->contains($promotion)) {
			$this->promotions->add($promotion);
			$promotion->addProduct($this);
		}

		return $this;
	}

	public function removePromotion(Promotion $promotion): self
	{
		if ($this->promotions->removeElement($promotion)) {
			$promotion->removeProduct($this);
		}

		return $this;
	}
}
