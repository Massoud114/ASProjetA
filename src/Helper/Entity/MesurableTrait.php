<?php

namespace App\Helper\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait MesurableTrait
{
	#[ORM\Column(type: Types::FLOAT, nullable: true)]
	private ?float $weight = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $weightUnit = 'kg';

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $brand = null;

	#[ORM\Column(type: Types::FLOAT, nullable: true)]
	private ?float $thickness = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $thicknessUnit = 'cm';

	#[ORM\Column(type: Types::FLOAT, nullable: true)]
	private ?float $width = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $widthUnit = 'cm';

	#[ORM\Column(type: Types::FLOAT, nullable: true)]
	private ?float $height = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $heightUnit = 'cm';

	#[ORM\Column(type: Types::FLOAT, nullable: true)]
	private ?float $length = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $lengthUnit = 'cm';

	public static function getWeightUnits(): array
	{
		return [
			'kg' => 'Kilogramme',
			'g' => 'Gramme',
			't' => 'tonne',
		];
	}

	public static function getLengthUnits(): array
	{
		return [
			'cm' => 'Centimètre',
			'mm' => 'millimètre',
			'm' => 'Mètre',
			'km' => 'Kilomètre',
		];
	}

	public function getWeight(): ?float
	{
		return $this->weight;
	}

	public function setWeight(?float $weight): self
	{
		$this->weight = $weight;

		return $this;
	}

	public function getWeightUnit(): ?string
	{
		return $this->weightUnit;
	}

	public function setWeightUnit(?string $weightUnit): self
	{
		$this->weightUnit = $weightUnit;
		return $this;
	}

	public function getBrand(): ?string
	{
		return $this->brand;
	}

	public function setBrand(?string $brand): self
	{
		$this->brand = $brand;
		return $this;
	}
	
	public function getThickness(): ?float
	{
		return $this->thickness;
	}

	public function setThickness(?float $thickness): self
	{
		$this->thickness = $thickness;
		return $this;
	}
	
	public function getThicknessUnit(): ?string
	{
		return $this->thicknessUnit;
	}

	public function setThicknessUnit(?string $thicknessUnit): self
	{
		$this->thicknessUnit = $thicknessUnit;
		return $this;
	}

	public function getWidth(): ?float
	{
		return $this->width;
	}

	public function setWidth(?float $width): self
	{
		$this->width = $width;
		return $this;
	}
	
	public function getWidthUnit(): ?string
	{
		return $this->widthUnit;
	}

	public function setWidthUnit(?string $widthUnit): self
	{
		$this->widthUnit = $widthUnit;
		return $this;
	}
	
	public function getHeight(): ?float
	{
		return $this->height;
	}

	public function setHeight(?float $height): self
	{
		$this->height = $height;
		return $this;
	}

	public function getHeightUnit(): ?string
	{
		return $this->heightUnit;
	}
	
	public function setHeightUnit(?string $heightUnit): self
	{
		$this->heightUnit = $heightUnit;
		return $this;
	}

	public function getLength(): ?float
	{
		return $this->length;
	}

	public function setLength(?float $length): self
	{
		$this->length = $length;
		return $this;
	}

	public function getLengthUnit(): ?string
	{
		return $this->lengthUnit;
	}
	
	public function setLengthUnit(?string $lengthUnit): self
	{
		$this->lengthUnit = $lengthUnit;
		return $this;
	}
	
}
