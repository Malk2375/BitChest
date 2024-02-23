<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)] // Rendre la colonne solde nullable
    private ?float $solde = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'], targetEntity: User::class, inversedBy: "wallet")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private ?User $user;

    #[ORM\Column(nullable: true)]
    private ?array $userCryptoAmounts = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUserCryptoAmounts(): ?array
    {
        return $this->userCryptoAmounts;
    }

    public function setUserCryptoAmounts(?array $userCryptoAmounts): static
    {
        $this->userCryptoAmounts = $userCryptoAmounts;

        return $this;
    }
}
