<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\Price;

final readonly class PricePresenter
{
    public function __construct(private CurrencyPresenter $currencyPresenter)
    {}

    public function present(Price $price): array
    {
        return [
           'amount' => $price->getAmount(),
           'currency' => $this->currencyPresenter->presentFromId($price->getCurrency()),
        ];
    }
}
