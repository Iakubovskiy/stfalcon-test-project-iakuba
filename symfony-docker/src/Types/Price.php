<?php
declare(strict_types=1);

namespace App\Types;

use App\Entity\Currency;

class Price{
    private float $amount;
    private Currency $currency;

    public function getAmount(): float{
        return $this->amount;
    }

    public function setAmount(float $amount): static{
        $this->amount = $amount;
        return $this;
    }

    public function getCurrency(): Currency{
        return $this->currency;
    }

    public function setCurrency(Currency $currency): static{
        $this->currency = $currency;
        return $this;
    }
}
