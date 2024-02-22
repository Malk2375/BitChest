<?php

namespace App\Entity;

use App\Repository\CryptoCurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CryptoCurrencyRepository::class)]
class CryptoCurrency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $currentPrice = null;

    #[ORM\Column]
    private array $dailyPrices = [];

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

    public function getCurrentPrice(): ?float
    {
        return $this->currentPrice;
    }

    public function setCurrentPrice(float $currentPrice): static
    {
        $this->currentPrice = $currentPrice;

        return $this;
    }

    public function getDailyPrices(): array
    {
        return $this->dailyPrices;
    }

    public function setDailyPrices(array $dailyPrices): static
    {
        $this->dailyPrices = $dailyPrices;

        return $this;
    }
}
