<?php

namespace App\Application\Media\Image;

use Doctrine\ORM\Mapping as ORM;
use App\Application\Product\Product;
use App\Application\Product\Entity\Color;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[Vich\UploadableField(mapping: 'images', fileNameProperty: 'name', size: 'size')]
	private ?File $imageFile = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: 'integer')]
	private ?int $size = null;

	#[ORM\Column(type: 'datetime')]
	private ?\DateTimeInterface $updatedAt = null;

	#[ORM\ManyToOne(inversedBy: 'productImages')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Product $product = null;

	#[ORM\ManyToOne]
	private ?Color $color = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getImageFile(): ?File
	{
		return $this->imageFile;
	}

	public function setImageFile(?File $imageFile = null): void
	{
		$this->imageFile = $imageFile;

		if (null !== $imageFile) {
			// It is required that at least one field changes if you are using doctrine
			// otherwise the event listeners won't be called and the file is lost
			$this->updatedAt = new \DateTimeImmutable();
		}
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function getSize(): ?int
	{
		return $this->size;
	}

	public function setSize(?int $size): void
	{
		$this->size = $size;
	}

	public function __toString(): string
	{
		return $this->name;
	}

	public function getProduct(): ?Product
	{
		return $this->product;
	}

	public function setProduct(?Product $product): self
	{
		$this->product = $product;

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
}
