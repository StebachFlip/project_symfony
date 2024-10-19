<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use App\Enum\mangaStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $price = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $stock = null;

    #[ORM\Column(type: 'string', enumType: MangaStatus::class)]
    private ?MangaStatus $status = null;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    private ?float $rating = null; // Ajout du champ rating

    // Relation ManyToMany avec Category
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'mangas')]
    #[ORM\JoinTable(name: 'manga_category')]
    private Collection $categories;

    // Relation OneToOne avec Image
    #[ORM\OneToOne(mappedBy: 'manga', targetEntity: Image::class)]
    private ?Image $image = null;

    // Relation ManyToMany avec Order via OrderItem
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'manga')]
    private Collection $orderItems;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
    }

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

    public function getAuthor(): ?string 
    {
        return $this->author;
    }
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStock(): ?bool
    {
        return $this->stock;
    }

    public function setStock(bool $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getStatus(): ?MangaStatus
    {
        return $this->status;
    }

    public function setStatus(MangaStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setManga($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // Set the owning side to null (unless already changed)
            if ($orderItem->getManga() === $this) {
                $orderItem->setManga(null);
            }
        }

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
