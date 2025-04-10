<?php
declare(strict_types=1);


namespace App\Fixtures;

use App\Entity\PropertyType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $typeResidential = new PropertyType();
        $typeResidential->setName('Residential Properties');
        $manager->persist($typeResidential);

        $typeCommercial = new PropertyType();
        $typeCommercial->setName('Commercial Properties');
        $manager->persist($typeCommercial);

        $typeLand = new PropertyType();
        $typeLand->setName('Land Properties');
        $manager->persist($typeLand);

        $manager->flush();
    }
}
