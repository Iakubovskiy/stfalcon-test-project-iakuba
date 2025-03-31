<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Entity\Currency;
use App\Services\CurrencyService;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CurrencyPresenter
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}
    public function present(Currency $currency): array
    {
        return [
            'id' => $currency->getId(),
            'name' => $currency->getName(),
        ];
    }

    public function presentFromId(string $currencyId): array
    {
        $currency = $this->entityManager->find(Currency::class, $currencyId);
        return $this->present($currency);
    }

    public function presentList(array $currencies): array
    {
        return array_map(fn (string $currency) => $this->present($currency), $currencies);
    }
}
