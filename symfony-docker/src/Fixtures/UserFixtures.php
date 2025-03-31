<?php
declare(strict_types=1);


namespace App\Fixtures;

use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {}

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            if ($i % 2 === 0) {
                $user = new Agent();
            } else {
                $user = new Customer();
            }
            $user -> setName('user '.$i);
            $user -> setEmail('user '.$i.'@example.com');
            $user -> setIsBlocked(false);
            $user -> setPhone('012345678'.$i);
            $user -> setPassword($this->hasher->hashPassword($user, 'pass_1234'));

            $manager->persist($user);
        }
        $manager->flush();
    }
}
