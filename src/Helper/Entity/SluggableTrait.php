<?php

namespace App\Helper\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SluggableTrait
{
	#[ORM\Column(length: 255, unique: true)]
	private string $slug;

	public function getSlug(): string
	{
		return $this->slug;
	}

	public function setSlug(string $slug): self
	{
		$this->slug = $slug;

		return $this;
	}
}
