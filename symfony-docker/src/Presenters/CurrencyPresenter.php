<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use App\Services\CurrencyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

final readonly class CurrencyPresenter
{
    public function __construct(
        private CurrencyRepository $currencyRepository,
    )
    {}
    public function present(Currency $currency): array
    {
        return [
            'id' => $currency->getId(),
            'name' => $currency->getName(),
        ];
    }

    public function presentFromId(Uuid $currencyId): array
    {
        $currency = $this->currencyRepository->find($currencyId);
        return $this->present($currency);
    }

    public function presentList(array $currencies): array
    {
        return array_map(fn (Currency $currency) => $this->present($currency), $currencies);
    }
}
