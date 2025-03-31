<?php
declare(strict_types=1);


namespace App\Fixtures;

use App\DTO\PropertyCreateDto;
use App\Entity\Agent;
use App\Entity\Property;
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
        private readonly EntityManagerInterface $entityManager,
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
        $users = $this->entityManager->getRepository(Agent::class)->findAll();
        $userCount = count($users);
        for ($i = 0; $i < 12; $i++) {
            $status = '';
            $type = '';
            $currency = '';
            $price = $i*1000 - 500;

            if($i % 3 === 0) {
                $type = 'residential';
                $currency = 'usd';
            } else if($i % 3 === 1) {
                $type = 'commercial';
                $currency = 'eur';
            } else if($i % 3 === 2) {
                $type = 'land';
                $currency = 'uah';
            }
            if($i % 5 === 0) {
                $status = 'draft';
            } else if($i % 5 === 1) {
                $status = 'available';
            } else if($i % 5 === 2) {
                $status = 'under_contract';
            } else if($i % 5 === 3) {
                $status = 'sold';
            } else if($i % 5 === 4) {
                $status = 'off_market';
            }

            $agent = $users[$i % $userCount];
            $agentId = $agent->getId();

            $data = new PropertyCreateDto(
                propertyTypeId: $type,
                agentId: $agentId,
                priceAmount: $price,
                priceCurrencyId: $currency,
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
