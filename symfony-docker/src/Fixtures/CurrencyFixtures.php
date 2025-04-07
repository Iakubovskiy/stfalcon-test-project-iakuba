<?php
declare(strict_types=1);


namespace App\Fixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $usd = new Currency();
        $usd->setName('USD');
        $manager->persist($usd);

        $uah = new Currency();
        $uah->setName('UAH');
        $manager->persist($uah);

        $eur = new Currency();
        $eur->setName('EUR');
        $manager->persist($eur);

        $manager->flush();
    }
}
