<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyService
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCurrencies(): array
    {
        return $this->entityManager->getRepository(Currency::class)->findAll();
    }

    public function createCurrency(string $id, string $name): Currency
    {
        $currency = new Currency();
        $currency->setId($id);
        $currency->setName($name);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }

    public function updateCurrency(string $currency_id, string $name): Currency
    {
        $currency = $this->entityManager->getRepository(Currency::class)->find($currency_id);
        $currency->setName($name);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();
        return $currency;
    }

    public function deleteCurrency(string $currency_id): bool
    {
        try {
            $currency = $this->entityManager->getRepository(Currency::class)->find($currency_id);
            $this->entityManager->remove($currency);
            $this->entityManager->flush();
            return true;
        }
        catch (\Exception $e) {
            throw new \Exception("Can't delete currency: " . $e->getMessage());
        }

    }

    public function coverCurrency(string $currency_from_id, string $currency_to_id, float $amount): float
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
                        break;
                    case 'eur':
                        return 0.93;
                        break;
                }
                break;
            case 'eur':
                switch ($currency_to_id) {
                    case 'uah':
                        return 44.74;
                        break;
                    case 'usd':
                        return 1.08;
                        break;
                }
                break;
            case 'uah':
                switch ($currency_to_id) {
                    case 'usd':
                        return 0.024;
                        break;
                    case 'eur':
                        return 0.022;
                        break;
                }
                break;
        }
        return 0;
    }
}
