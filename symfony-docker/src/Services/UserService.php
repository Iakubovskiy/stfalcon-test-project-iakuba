<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\Entity\Property;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{

    public function __construct(private EntityManagerInterface $em, private UserPasswordHasherInterface $passwordHarsher)
    {}

    public function register(RegisterDto $registerDto): User {
        $existingUser = $this->em->getRepository(User::class)->findOneBy(["email"=> $registerDto->email]);
        if($existingUser) {
            return new Customer();
        }

        $user = match ($registerDto->role) {
            "ROLE_AGENT" => new Agent(),
            'ROLE_CUSTOMER' => new Customer(),
            default => throw new \Exception('invalid role'),
        };
        $user->setName($registerDto->name);
        $user->setEmail($registerDto->email);
        $hashed_password = $this->passwordHarsher->hashPassword($user, $registerDto->password);
        $user->setPassword($hashed_password);
        $user->setPhone($registerDto->phone);
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

    public function unblockUser(Uuid $userId): void
    {
        $user = $this->em->getRepository(User::class)->find($userId);
        $user->setIsBlocked(false);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function updateProfile(Uuid $userId, UpdateProfileDto $newUser): User {
        $existingUser = $this->em->getRepository(User::class)->find($userId);
        if ($newUser->name)
            $existingUser->setName($newUser->name);
        if ($newUser->email)
            $existingUser->setEmail($newUser->email);
        if ($newUser->password) {
            $hashed_password = $this->passwordHarsher->hashPassword($existingUser, $newUser->password);
            $existingUser->setPassword($hashed_password);
        }
        if ($newUser->phone) {
            $existingUser->setPhone($newUser->phone);
        }
        $this->em->persist($existingUser);
        $this->em->flush();

        return $existingUser;
    }

    public function getAllUsers(int $offset, int $limit): array
    {
        $query = $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return [
            'result' => iterator_to_array($paginator),
            'total' => $paginator->count(),
            'offset' => $offset,
            'limit' => $limit,
        ];
    }

    public function getUserById(Uuid $userId): User
    {
        return $this->em->getRepository(User::class)->find($userId);
    }

    public function addFavorite(Uuid $userId, Uuid $propertyId): Customer
    {
        $customer = $this->em->getRepository(Customer::class)->find($userId);
        $property = $this->em->getRepository(Property::class)->find($propertyId);
        $customer->addFavoriteProperty($property);
        $this->em->persist($customer);
        $this->em->flush();
        return $customer;
    }

    public function removeFavorite(Uuid $userId, Uuid $propertyId): Customer
    {
        $customer = $this->em->getRepository(Customer::class)->find($userId);
        $property = $this->em->getRepository(Property::class)->find($propertyId);
        $customer->removeFavoriteProperty($property);
        $this->em->persist($customer);
        $this->em->flush();
        return $customer;
    }
}
