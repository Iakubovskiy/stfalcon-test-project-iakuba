<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Admin;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHarsher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHarsher) {
        $this->em = $em;
        $this->passwordHarsher = $passwordHarsher;
    }

    public function register(string $email, string $password, string $role, string $name, string $phone): User {
        $existingUser = $this->em->getRepository(User::class)->findOneBy(["email"=> $email]);
        if($existingUser) {
            throw new \Exception("This email is already used");
        }

        $user = match ($role) {
            "ROLE_AGENT" => new Agent(),
            'ROLE_ADMIN' => new Admin(),
            'ROLE_CUSTOMER' => new Customer(),
            default => throw new \Exception('invalid role'),
        };
        $user->setName($name);
        $user->setEmail($email);
        $hashed_password = $this->passwordHarsher->hashPassword($user, $password);
        $user->setPassword($hashed_password);
        $user->setPhone($phone);
        $user->setIsBlocked(false);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function blockUser(Uuid $userId): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $user->setIsBlocked(true);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function unBlockUser(Uuid $userId): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $user->setIsBlocked(false);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function updateProfile(Uuid $userId, string $email, string $password, string $name, string $phone): User {
        $existingUser = $this->em->getRepository(User::class)->find($userId);

        $existingUser->setName($name);
        $existingUser->setEmail($email);
        $hashed_password = $this->passwordHarsher->hashPassword($existingUser, $password);
        $existingUser->setPassword($hashed_password);
        $existingUser->setPhone($phone);
        $existingUser->setIsBlocked(false);

        $this->em->persist($existingUser);
        $this->em->flush();

        return $existingUser;
    }

    public function getAllUsers(): array
    {
        return $this->em->getRepository(User::class)->findAll();
    }
}
