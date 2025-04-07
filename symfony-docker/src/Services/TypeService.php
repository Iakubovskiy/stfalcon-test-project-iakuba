<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\PropertyType;
use App\Repository\PropertyTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class TypeService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyTypeRepository $propertyTypeRepository,
    )
    {}

    public function getTypes(): array
    {
        return $this->propertyTypeRepository->findAll();
    }

    public function createType(string $name): PropertyType
    {
        $type = new PropertyType();
        $type->setName($name);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
        return $type;
    }

    public function updateType(Uuid $propertyTypeId, string $name): PropertyType
    {
        $type = $this->propertyTypeRepository->find($propertyTypeId);
        $type->setName($name);
        $this->entityManager->persist($type);
        $this->entityManager->flush();
        return $type;
    }

    public function deleteType(Uuid $propertyTypeId): bool
    {
        $type = $this->propertyTypeRepository->find($propertyTypeId);
        $this->entityManager->remove($type);
        $this->entityManager->flush();
        return true;
    }
}
