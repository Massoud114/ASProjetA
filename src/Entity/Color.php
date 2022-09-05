<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ColorRepository;

#[ORM\Entity(repositoryClass: ColorRepository::class)]
class Color
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 6)]
    private ?string $hexColor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHexColor(): ?string
    {
        return $this->hexColor;
    }

    public function setHexColor(string $hexColor): self
    {
        $this->hexColor = $hexColor;

        return $this;
    }
	public function getRed(): string
	{
		return hexdec(substr($this->hexColor, 0, 2));
	}

	public function getGreen(): string
	{
		return hexdec(substr($this->hexColor, 2, 2));
	}

	public function getBlue(): string
	{
		return hexdec(substr($this->hexColor, 4, 2));
	}
}
