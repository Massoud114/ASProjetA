<?php
namespace App\Helper\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TimestampTrait
{
	#[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
	private \DateTimeInterface $createdAt;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private \DateTimeInterface $updatedAt;

	public function getCreatedAt(): \DateTimeInterface
	{
		return $this->createdAt;
	}

	public function getUpdatedAt(): \DateTimeInterface
	{
		return $this->updatedAt;
	}

	#[ORM\PrePersist]
	public function prePersist(): void
	{
		$this->createdAt = new \DateTimeImmutable();
		$this->updatedAt = new \DateTimeImmutable();
	}

	#[ORM\PreUpdate]
	public function preUpdate(): void
	{
		$this->updatedAt = new \DateTimeImmutable();
	}
}
