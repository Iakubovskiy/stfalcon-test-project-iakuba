<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\PropertyStatus;
use Doctrine\ORM\EntityManagerInterface;

class StatusService
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllStatuses(): array
    {
        try {
            return $this->entityManager->getRepository(PropertyStatus::class)->findAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function create(string $statusId, string $name): PropertyStatus
    {
        try {
            $status = new PropertyStatus();
            $status->setId($statusId);
            $status->setName($name);
            $this->entityManager->persist($status);
            $this->entityManager->flush();
            return $status;
        } catch (\Exception $e) {
            throw new \Exception("Can't create status: " . $e->getMessage());
        }
    }

    public function update(string $statusId, string $name): PropertyStatus
    {
        try {
            $status = $this->entityManager->getRepository(PropertyStatus::class)->find($statusId);
            $status->setName($name);
            $this->entityManager->flush();
            return $status;
        }catch (\Exception $e) {
            throw new \Exception("Can't update status: " . $e->getMessage());
        }
    }

    public function delete(string $statusId): bool
    {
        try {
            $status = $this->entityManager->getRepository(PropertyStatus::class)->find($statusId);
            $this->entityManager->remove($status);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Can't delete status: " . $e->getMessage());
        }
    }
}
