<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class CurrencyService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private CurrencyRepository $currencyRepository,
    )
    {}

    public function getCurrencies(): array
    {
        return $this->currencyRepository->findAll();
    }

    public function createCurrency(string $name): Currency
    {
        $currency = new Currency();
        $currency->setName($name);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }

    public function updateCurrency(Uuid $currency_id, string $name): Currency
    {
        $currency = $this->currencyRepository->find($currency_id);
        $currency->setName($name);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();
        return $currency;
    }

    public function deleteCurrency(Uuid $currency_id): bool
    {
        $currency = $this->currencyRepository->find($currency_id);
        $this->entityManager->remove($currency);
        $this->entityManager->flush();
        return true;
    }

    public function convertCurrency(string $currency_from_id, string $currency_to_id, float $amount): float
    {
        if ($currency_from_id === $currency_to_id) {
            return $amount;
        }
        $exchangeRate = $this->getExchangeRates($currency_from_id, $currency_to_id);
        return $amount * $exchangeRate;
    }

    private function getExchangeRates(string $currency_from_id, string $currency_to_id): float
    {
        switch ($currency_from_id) {
            case 'usd':
                switch ($currency_to_id) {
                    case 'uah':
                        return 41.45;
                    case 'eur':
                        return 0.93;
                }
                break;
            case 'eur':
                switch ($currency_to_id) {
                    case 'uah':
                        return 44.74;
                    case 'usd':
                        return 1.08;
                }
                break;
            case 'uah':
                switch ($currency_to_id) {
                    case 'usd':
                        return 0.024;
                    case 'eur':
                        return 0.022;
                }
                break;
        }
        return 0;
    }
}
