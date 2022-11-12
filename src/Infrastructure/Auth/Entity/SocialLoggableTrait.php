<?php

namespace App\Infrastructure\Auth\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait SocialLoggableTrait
{
	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $tiktokId = null;

	#[ORM\Column(type: Types::STRING, nullable: true)]
	private ?string $googleId = null;

	public function getTiktokId(): ?string
	{
		if ($this->tiktokId === "")
			return null;
		return $this->tiktokId;
	}

	public function setTiktokId(?string $tiktokId): self
	{
		$this->tiktokId = $tiktokId;

		return $this;
	}

	public function getGoogleId(): ?string
	{
		if ($this->googleId === "")
			return null;
		return $this->googleId;
	}

	public function setGoogleId(?string $googleId): self
	{
		$this->googleId = $googleId;

		return $this;
	}

	public function useOauth(): bool
	{
		return (null !== $this->googleId || null !== $this->tiktokId) && $this->password === '';
	}
}
