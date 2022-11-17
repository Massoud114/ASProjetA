<?php

namespace App\Application\Product;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Helper\Entity\MesurableTrait;
use App\Helper\Entity\SluggableTrait;
use App\Helper\Entity\TimestampTrait;
use App\Application\Media\Image\Image;
use App\Application\Product\Entity\Color;
use Doctrine\Common\Collections\Collection;
use App\Application\Product\Entity\Category;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private array $types = [];

    #[ORM\Column]
    private int $minPrice;

    #[ORM\Column(nullable: true)]
    private ?int $maxPrice = null;

    #[ORM\Column]
    private int $stockQuantity = 0;

    #[ORM\Column(nullable: true)]
    private ?float $makingPrice = null;

	#[ORM\Column(nullable: true)]
    private ?float $fixedPrice = 0;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

	#[ORM\ManyToMany(targetEntity: Color::class)]
	private Collection $colors;

	#[ORM\Column(nullable: false)]
	private ?string $thumbnailUrl = null;

	#[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class)]
	private Collection $productImages;

	#[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
	private bool $visible = true;

	#[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
	private bool $favorite = false;

	use MesurableTrait, SluggableTrait, TimestampTrait;

	public function __construct()
	{
		$this->colors = new ArrayCollection();
		$this->productImages = new ArrayCollection();
		$this->createdAt = new \DateTimeImmutable();
		$this->updatedAt = new \DateTime();
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

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(?array $types): self
    {
        $this->types = $types;

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
		return (string) $this->maxPrice / 100;
	}

    public function getStockQuantity(): int
    {
        return $this->stockQuantity || 0;
    }

    public function setStockQuantity(int $stockQuantity): self
    {
        $this->stockQuantity = $stockQuantity;

        return $this;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

	/**
	 * @return Collection|Color[]
	 */
	public function getColors(): Collection|array
	{
		return $this->colors;
	}

	public function addColor(Color $color): self
	{
		if (!$this->colors->contains($color)) {
			$this->colors[] = $color;
		}

		return $this;
	}

	public function removeColor(Color $color): self
	{
		if ($this->colors->contains($color)) {
			$this->colors->removeElement($color);
		}

		return $this;
	}

	public function hasColors(): bool
	{
		return count($this->colors) > 0;
	}

	public function __toString(): string
	{
		return sprintf('%s ($%s)', $this->name, $this->getFixedPriceString());
	}

	public function getFixedPriceString(): string
	{
		return (string) $this->fixedPrice / 100;
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

	/**
	 * @return Collection<int, \App\Application\Media\Image\Image>
	 */
	public function getProductImages(): Collection
	{
		return $this->productImages;
	}

	public function addProductImage(Image $image): self
	{
		if (!$this->productImages->contains($image)) {
			$this->productImages->add($image);
			$image->setProduct($this);
		}

		return $this;
	}

	public function removeProductImage(Image $image): self
	{
		if ($this->productImages->removeElement($image)) {
			// set the owning side to null (unless already changed)
			if ($image->getProduct() === $this) {
				$image->setProduct(null);
			}
		}

		return $this;
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

}
