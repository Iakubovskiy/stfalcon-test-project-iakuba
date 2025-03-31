<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\PropertyCreateDto;
use App\DTO\PropertyUpdateDto;
use App\Entity\Agent;
use App\Entity\Coordinates;
use App\Entity\Price;
use App\Entity\Property;
use App\Entity\PropertyLocation;
use App\Entity\PropertySize;
use App\Entity\PropertyStatus;
use App\Entity\PropertyType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class PropertyService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {}

    public function getAllProperties(int $offset, int $limit): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Property::class, 'p')
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

    public function getProperty(Uuid $propertyId): ?Property
    {
        try {
            return $this->entityManager->getRepository(Property::class)->find($propertyId);
        } catch (EntityNotFoundException $exception) {
            return null;
        }
    }

    public function getAllPropertiesForUsers(array $visibleStatuses,int $offset, int $limit):array
    {
        try {
            $query = $this->entityManager->createQueryBuilder()
                ->select('p')
                ->from(Property::class, 'p')
                ->where('p.status IN (:statuses)')
                ->setParameter('statuses', $visibleStatuses)
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
        } catch (EntityNotFoundException $exception) {
            return [];
        }
    }

    public function getAgentProperty(Uuid $agentId): array
    {
        try {
            $agent = $this->entityManager->getRepository(Agent::class)->find($agentId);
            return iterator_to_array($agent->getProperties());
        } catch (EntityNotFoundException $exception) {
            return [];
        }
    }

    public function createProperty(
        PropertyCreateDto $propertyCreateDto,
    ): Property
    {
        try {
            $property = new Property();
            $agent = $this->entityManager->getRepository(Agent::class)->find($propertyCreateDto->agentId);
            $type = $this->entityManager->getRepository(PropertyType::class)->find($propertyCreateDto->propertyTypeId);
            $price = new Price();
            $location = new PropertyLocation();
            $size = new PropertySize();

            $price->setAmount($propertyCreateDto->priceAmount);
            $price->setCurrency($propertyCreateDto->priceCurrencyId);

            $coordinates = new Coordinates();
            $coordinates->setLatitude($propertyCreateDto->latitude);
            $coordinates->setLongitude($propertyCreateDto->longitude);
            $location->setAddress($propertyCreateDto->address);
            $location->setCoordinates($coordinates);

            $size->setValue($propertyCreateDto->area);
            $size->setMeasurement($propertyCreateDto->measurement);

            $status = $this->entityManager->getRepository(PropertyStatus::class)->find('draft');

            $property->setAgent($agent);
            $property->setType($type);
            $property->setPrice($price);
            $property->setLocation($location);
            $property->setSize($size);
            $property->setDescription($propertyCreateDto->description);
            $property->setStatus($status);

            $this->entityManager->persist($property);
            $this->entityManager->flush();
            return $property;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function updateProperty(Uuid $propertyId, PropertyUpdateDto $propertyUpdateDto) : Property
    {
        try {
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
            if ($propertyUpdateDto->propertyTypeId) {
                $type = $this->entityManager->getRepository(PropertyType::class)->find($propertyUpdateDto->propertyTypeId);
                $property->setType($type);
            }
            if ($propertyUpdateDto->priceAmount) {
                $property->getPrice()->setAmount($propertyUpdateDto->priceAmount);
            }
            if($propertyUpdateDto->priceCurrencyId){
                $property->getPrice()->setCurrency($propertyUpdateDto->priceCurrencyId);
            }
            if($propertyUpdateDto->address){
                $property->getLocation()->setAddress($propertyUpdateDto->address);
            }
            if($propertyUpdateDto->latitude){
                $property->getLocation()->getCoordinates()->setLatitude($propertyUpdateDto->latitude);
            }
            if($propertyUpdateDto->longitude){
                $property->getLocation()->getCoordinates()->setLongitude($propertyUpdateDto->longitude);
            }
            if($propertyUpdateDto->area){
                $property->getSize()->setValue($propertyUpdateDto->area);
            }
            if($propertyUpdateDto->measurement){
                $property->getSize()->setMeasurement($propertyUpdateDto->measurement);
            }
            if($propertyUpdateDto->description) {
                $property->setDescription($propertyUpdateDto->description);
            }
            $this->entityManager->persist($property);
            $this->entityManager->flush();
            return $property;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function deleteProperty(Uuid $propertyId): bool
    {
        try {
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
            $this->entityManager->remove($property);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function changePropertyStatus(Uuid $propertyId, string $statusId): Property
    {
        try {
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
            $status = $this->entityManager->getRepository(PropertyStatus::class)->find($statusId);
            $property->setStatus($status);
            $this->entityManager->persist($property);
            $this->entityManager->flush();
            return $property;
        } catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
}
