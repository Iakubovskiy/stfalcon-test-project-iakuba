<?php
declare(strict_types=1);


namespace App\Fixtures;

use App\Entity\PropertyStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StatusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $statusDraft = new PropertyStatus();
        $statusDraft->setName('Draft');
        $manager->persist($statusDraft);

        $statusAvailable = new PropertyStatus();
        $statusAvailable->setName('Available');
        $manager->persist($statusAvailable);

        $statusUnderContract = new PropertyStatus();
        $statusUnderContract->setName('Under Contract');
        $manager->persist($statusUnderContract);

        $statusSold = new PropertyStatus();
        $statusSold->setName('Sold');
        $manager->persist($statusSold);

        $statusOffMarket = new PropertyStatus();
        $statusOffMarket->setName('Off Market');
        $manager->persist($statusOffMarket);

        $manager->flush();
    }
}
