<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $saleDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $totalAmount = null;

    /**
     * @var Collection<int, SaleItem>
     */
    #[ORM\OneToMany(targetEntity: SaleItem::class, mappedBy: 'sale', cascade: ['persist', 'remove'])]
    private Collection $saleItems;

    public function __construct()
    {
        $this->saleItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaleDate(): ?\DateTimeImmutable
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTimeImmutable $saleDate): static
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * @return Collection<int, SaleItem>
     */
    public function getSaleItems(): Collection
    {
        return $this->saleItems;
    }

    public function addSaleItem(SaleItem $saleItem): static
    {
        if (!$this->saleItems->contains($saleItem)) {
            $this->saleItems->add($saleItem);
            $saleItem->setSale($this);
        }

        return $this;
    }

    public function removeSaleItem(SaleItem $saleItem): static
    {
        if ($this->saleItems->removeElement($saleItem)) {
            if ($saleItem->getSale() === $this) {
                $saleItem->setSale(null);
            }
        }

        return $this;
    }
}
