<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Price{
    #[ORM\Column(type:'float')]
    private float $amount;
    #[ORM\Column(length: 3)]
    private string $currencyId;

    public function getAmount(): float{
        return $this->amount;
    }

    public function setAmount(float $amount): static{
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): string{
        return $this->currencyId;
    }

    public function setCurrency(string $currency): static{
        $this->currencyId = $currency;
        return $this;
    }
}
