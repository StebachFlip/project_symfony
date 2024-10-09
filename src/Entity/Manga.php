<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use App\Repository\MangaRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $price = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $stock = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(choices: ['Disponible', 'Indisponible', 'Précommande'], message: 'Choisissez un statut valide.')]
    private string $status;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isStock(): ?bool
    {
        return $this->stock;
    }

    public function setStock(bool $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        // Optionnel: Valider avant de setter le statut
        $validStatuses = ['Disponible', 'Indisponible', 'Précommande'];
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Statut invalide");
        }

        $this->status = $status;
        return $this;
    }
}
