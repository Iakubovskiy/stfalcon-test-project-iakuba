<?php
declare(strict_types=1);


namespace App\Fixtures;

use App\DTO\PropertyCreateDto;
use App\Entity\Agent;
use App\Entity\Property;
use App\Repository\AgentRepository;
use App\Repository\CurrencyRepository;
use App\Repository\CustomerRepository;
use App\Repository\PropertyStatusRepository;
use App\Repository\PropertyTypeRepository;
use App\Repository\UserRepository;
use App\Services\PropertyService;
use App\Services\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\String\b;

class ZPropertyFixtures extends Fixture
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly AgentRepository $agentRepository,
        private readonly PropertyTypeRepository $propertyTypeRepository,
        private readonly CurrencyRepository $currencyRepository,
    )
    {}

    public function getDependencies(): array
    {
        return [
            CurrencyFixtures::class,
            StatusFixtures::class,
            TypeFixtures::class,
            UserFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void
    {
        $users = $this->agentRepository->findAll();
        $types = $this->propertyTypeRepository->findAll();
        $currencies = $this->currencyRepository->findAll();

        $userCount = count($users);
        $typeCount = count($types);
        $currenciesCount = count($currencies);

        for ($i = 0; $i < 12; $i++) {
            $price = $i*1000 - 500;

            $agent = $users[$i % $userCount];
            $type = $types[$i % $typeCount];
            $currency = $currencies[$i % $currenciesCount];

            $agentId = $agent->getId();
            $typeId = $type->getId();
            $currencyId = $currency->getId();

            $data = new PropertyCreateDto(
                propertyTypeId: $typeId,
                agentId: $agentId,
                priceAmount: $price,
                priceCurrencyId: $currencyId,
                latitude: 50.4501,
                longitude: 30.5236,
                address: 'Some address ' . $i,
                area: 100.5,
                measurement: 'm2',
                description: 'Description for property ' . $i
            );

            $this->propertyService->createProperty($data);
        }
    }
}
