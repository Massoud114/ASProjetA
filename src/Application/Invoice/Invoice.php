<?php

namespace App\Application\Invoice;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Application\Purchase\Purchase;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
	const STATES = [
		1 => "Non envoyé",
		2 => "Envoyé",
		3 => "Annulé"
	];
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Purchase $purchase = null;

    #[ORM\Column(length: 255)]
    private ?string $filename = null;

    #[ORM\Column]
    private int $state = 1;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sent_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPurchase(): ?Purchase
    {
        return $this->purchase;
    }

    public function setPurchase(Purchase $purchase): self
    {
        $this->purchase = $purchase;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getState(): ?int
    {
	    return self::STATES[$this->state];
	}

    public function setState(int $state): self
    {
        $this->state = $state;

	    //TODO : Throw an error
	    /*if (!in_array($state, array_keys(self::STATES))){
		}*/
        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sent_at;
    }

    public function setSentAt(?\DateTimeInterface $sent_at): self
    {
        $this->sent_at = $sent_at;

        return $this;
    }

	public function isSent() : bool
	{
		return $this->createdAt > new \DateTime();
	}

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
