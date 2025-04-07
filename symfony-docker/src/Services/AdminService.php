<?php
declare(strict_types=1);


namespace App\Services;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminService
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHarsher,
        private readonly EntityManagerInterface $entityManager,
    )
    {

    }

    public function createAdmin(
        string $name,
        string $email,
        string $password,
        string $phone,
    ): Admin
    {
        $user = new Admin();

        $user->setName($name);
        $user->setEmail($email);
        $hashed_password = $this->passwordHarsher->hashPassword($user, $password);
        $user->setPassword($hashed_password);
        $user->setPhone($phone);
        $user->setIsBlocked(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}
