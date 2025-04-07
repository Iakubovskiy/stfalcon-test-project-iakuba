<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\RegisterDto;
use App\DTO\UpdateProfileDto;
use App\Entity\Property;
use App\Entity\Agent;
use App\Entity\Customer;
use App\Entity\User;
use App\Enum\Role;
use App\Repository\AgentRepository;
use App\Repository\CustomerRepository;
use App\Repository\PropertyRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly CustomerRepository $customerRepository,
        private readonly PropertyRepository $propertyRepository,
        private readonly UserPasswordHasherInterface $passwordHarsher,
        private readonly EntityManagerInterface $em,
    )
    {}

    public function register(RegisterDto $registerDto): User {
        $existingUser = $this->userRepository->findOneBy(["email"=> $registerDto->email]);
        if($existingUser) {
            return new Customer();
        }

        $user = match ($registerDto->role) {
            Role::AGENT->value => new Agent(),
            Role::CUSTOMER->value => new Customer(),
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
        $user = $this->userRepository->find($userId);
        $user->setIsBlocked(true);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function unblockUser(Uuid $userId): void
    {
        $user = $this->userRepository->find($userId);
        $user->setIsBlocked(false);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function updateProfile(Uuid $userId, UpdateProfileDto $newUser): User {
        $existingUser = $this->userRepository->find($userId);
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
        return $this->userRepository->find($userId);
    }

    public function addFavorite(Uuid $userId, Uuid $propertyId): Customer
    {
        $customer = $this->customerRepository->find($userId);
        $property = $this->propertyRepository->find($propertyId);
        $customer->addFavoriteProperty($property);
        $this->em->persist($customer);
        $this->em->flush();
        return $customer;
    }

    public function removeFavorite(Uuid $userId, Uuid $propertyId): Customer
    {
        $customer = $this->customerRepository->find($userId);
        $property = $this->propertyRepository->find($propertyId);
        $customer->removeFavoriteProperty($property);
        $this->em->persist($customer);
        $this->em->flush();
        return $customer;
    }
}
