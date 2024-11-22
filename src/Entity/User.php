<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: 'boolean')]
    private bool $role = false; // Rôles peuvent être stockés dans un tableau JSON

    #[ORM\Column(type: 'string')]
    private ?string $password = null;

    // Relation OneToMany avec Order
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class, cascade: ['persist', 'remove'])]
    private Collection $orders;

    // Relation OneToOne avec Address
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Address $address = null;

    // Relation OneToOne avec ProfilePicture
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?ProfilePicture $profilePicture = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getRole(): bool
    {
        return $this->role;
    }

    public function setRole(bool $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // Set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        // Set the owning side of the relation if necessary
        if ($address !== null && $address->getUser() !== $this) {
            $address->setUser($this);
        }

        return $this;
    }

    public function eraseCredentials(): void {
        //
    }

    public function getRoles(): array {
        return ['ROLES_USER'];
    }

    // Méthode nécessaire pour PasswordAuthenticatedUserInterface
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getProfilePicture(): ?ProfilePicture
    {
        return $this->profilePicture;
    }

    // public function setProfilePicture(?ProfilePicture $profilePicture): self
    // {
    //     // Unset the owning side of the relation if necessary
    //     if ($profilePicture === null && $this->profilePicture !== null) {
    //         $this->profilePicture->setUser(null);
    //     }

    //     // Set the owning side of the relation if necessary
    //     if ($profilePicture !== null && $profilePicture->getUser() !== $this) {
    //         $profilePicture->setUser($this);
    //     }

    //     $this->profilePicture = $profilePicture;

    //     return $this;
    // }
}
