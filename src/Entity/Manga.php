<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use App\Enum\mangaStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

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

    #[ORM\Column(type: 'integer')]
    private ?int $stock = null;

    #[ORM\Column(type: 'string', enumType: mangaStatus::class)]
    private ?mangaStatus $status = null;

    #[ORM\Column(type: 'float', options: ['default' => 0])]
    private ?float $rating = 0; // Champ de notation avec une valeur par défaut de 0

    // Relation ManyToMany avec Category
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'mangas')]
    #[ORM\JoinTable(name: 'manga_category')]
    #[MaxDepth(1)] // Limiter la profondeur de sérialisation
    private Collection $categories;

    // Relation OneToOne avec Image
    #[ORM\OneToOne(mappedBy: 'manga', targetEntity: Image::class)]
    #[MaxDepth(1)] // Limiter la profondeur de sérialisation
    private ?Image $image = null;

    // Relation OneToMany avec Order via OrderItem
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'manga')]
    private Collection $orderItems;

    #[ORM\Column(type: 'text')]
    private ?string $link = null;

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

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getStatus(): ?mangaStatus
    {
        return $this->status;
    }

    public function setStatus(mangaStatus $status): self
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

    public function updateRating(int $numberOfReviews, float $newRating): self
    {
        // Calculer la nouvelle note
        if ($numberOfReviews > 0) {
            $currentRating = $this->rating;
            $this->rating = (($currentRating * ($numberOfReviews - 1)) + $newRating) / $numberOfReviews;
        } else {
            $this->rating = $newRating; // Si c'est le premier avis
        }

        return $this;
    }

    public function getLink(): string {
        return $this->link;
    }

    public function setLink(string $link) {
        $this->link = $link;
    }
}
