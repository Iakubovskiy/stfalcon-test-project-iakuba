<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\PropertyType;
use Doctrine\ORM\EntityManagerInterface;

class TypeService
{

    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function getTypes(): array
    {
        return $this->entityManager->getRepository(PropertyType::class)->findAll();
    }

    public function createType(string $propertyTypeId, string $name): PropertyType
    {
        try {
            $type = new PropertyType();
            $type->setId($propertyTypeId);
            $type->setName($name);
            $this->entityManager->persist($type);
            $this->entityManager->flush();
            return $type;
        } catch (\Exception $e){
            throw new \Exception("Can't create type: " . $e->getMessage());
        }
    }

    public function updateType(string $propertyTypeId, string $name): PropertyType
    {
        try {
            $type = $this->entityManager->getRepository(PropertyType::class)->find($propertyTypeId);
            $type->setName($name);
            $this->entityManager->persist($type);
            $this->entityManager->flush();
            return $type;
        } catch (\Exception $e){
            throw new \Exception("Can't update type: " . $e->getMessage());
        }
    }

    public function deleteType(string $propertyTypeId): bool
    {
        try {
            $type = $this->entityManager->getRepository(PropertyType::class)->find($propertyTypeId);
            $this->entityManager->remove($type);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e){
            throw new \Exception("Can't delete type: " . $e->getMessage());
        }
    }
}
