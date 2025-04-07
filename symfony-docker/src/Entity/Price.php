<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Embeddable]
class Price{
    #[ORM\Column(type:'float')]
    private float $amount;
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $currencyId;

    public function getAmount(): float{
        return $this->amount;
    }

    public function setAmount(float $amount): self{
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): Uuid{
        return $this->currencyId;
    }

    public function setCurrency(Uuid $currency): self{
        $this->currencyId = $currency;
        return $this;
    }
}
