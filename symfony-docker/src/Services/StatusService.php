<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\PropertyStatus;
use App\Repository\PropertyStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class StatusService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PropertyStatusRepository $propertyStatusRepository,
    )
    {}

    public function getAllStatuses(): array
    {
        return $this->propertyStatusRepository->findAll();
    }

    public function create(string $name): PropertyStatus
    {
        $status = new PropertyStatus();
        $status->setName($name);
        $this->entityManager->persist($status);
        $this->entityManager->flush();
        return $status;
    }

    public function update(Uuid $statusId, string $name): PropertyStatus
    {
        $status = $this->propertyStatusRepository->find($statusId);
        $status->setName($name);
        $this->entityManager->flush();
        return $status;
    }

    public function delete(Uuid $statusId): bool
    {
            $status = $this->propertyStatusRepository->find($statusId);
            $this->entityManager->remove($status);
            $this->entityManager->flush();
            return true;
    }
}
