<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CardRepository::class)]
class Card
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 16)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 16,
        max: 16,
        exactMessage: "The card number must be exactly 16 digits."
    )]
    private ?string $number = null;

    #[ORM\Column(type: 'string', length: 5)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^(0[1-9]|1[0-2])\/\d{2}$/',
        message: "The expiration date must be in MM/YY format."
    )]
    private ?string $expirationDate = null;

    #[ORM\Column(type: 'string', length: 3)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 3,
        exactMessage: "The CVV must be exactly 3 digits."
    )]
    private ?string $cvv = null;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getExpirationDate(): ?string
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(string $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getCvv(): ?string
    {
        return $this->cvv;
    }

    public function setCvv(string $cvv): self
    {
        $this->cvv = $cvv;

        return $this;
    }

    public function getLastDigits(): string
    {
        return substr($this->number, -4); // Supposez que $this->number contient le numéro complet de la carte
    }

}
