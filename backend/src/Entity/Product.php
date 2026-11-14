<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[Groups(["category", "product"])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    
    #[Groups(["category", "product"])]
    #[ORM\Column(length: 128)]
    private ?string $name = null;

    #[Groups(["category", "product"])]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $price = null;

    #[Groups(["category", "product"])]
    #[ORM\Column]
    private ?int $stock = null;
    
    #[Groups(["category", "product"])]
    #[ORM\Column(options: ['default' => true])]
    private ?bool $isAvailable = true;

    // #[Groups(["Product-category"])]
    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(?bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    #[Groups(['product'])]
    public function getStatus(): string
    {
        if ($this->stock === 0) {
            return 'OUT_OF_STOCK';
        }

        if ($this->stock <= 5) {
            return 'LOW_STOCK';
        }

        return 'AVAILABLE';
    }

    #[Groups(['product'])]
    public function getCategoryName(): ?string
    {
        return $this->category?->getName();
    }
}
