<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Agent;
use App\Entity\Currency;
use App\Entity\Property;
use App\Entity\PropertyType;
use App\Entity\PropertyStatus;
use App\Types\Coordinates;
use App\Types\Price;
use App\Types\PropertyLocation;
use App\Types\PropertySize;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Uid\Uuid;

class PropertyService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAllProperties(): array
    {
        return $this->entityManager->getRepository(Property::class)->findAll();
    }

    public function getProperty(Uuid $propertyId): ?Property
    {
        try {
            return $this->entityManager->getRepository(Property::class)->find($propertyId);
        } catch (EntityNotFoundException $exception) {
            return null;
        }
    }

    public function getAllPropertiesForUser(array $notVisibleStatuses):array
    {
        try {
            return $this->entityManager->getRepository(Property::class)
               ->createQueryBuilder('p')
                ->where('p.status NOT IN (:statuses)')
                ->setParameter('statuses', $notVisibleStatuses)
                ->getQuery()
                ->getResult();
        } catch (EntityNotFoundException $exception) {
            return [];
        }
    }

    public function getAgentProperty(Uuid $agentId): array
    {
        try {
            $agent = $this->entityManager->getRepository(Agent::class)->find($agentId);
            return $agent->getProperties();
        } catch (EntityNotFoundException $exception) {
            return [];
        }
    }

    public function createProperty(
        Uuid $agentId,
        string $propertyTypeId,
        float $priceAmount,
        string $priceCurrencyId,
        string $address,
        float $latitude,
        float $longitude,
        float $area,
        string $measurement,
        string $description,
    ): Property
    {
        try {
            $property = new Property();
            $agent = $this->entityManager->getRepository(Agent::class)->find($agentId);
            $type = $this->entityManager->getRepository(PropertyType::class)->find($propertyTypeId);
            $price = new Price();
            $location = new PropertyLocation();
            $size = new PropertySize();

            $price->setAmount($priceAmount);
            $priceCurrency = $this->entityManager->getRepository(Currency::class)->find($priceCurrencyId);
            $price->setCurrency($priceCurrency);

            $coordinates = new Coordinates();
            $coordinates->setLatitude($latitude);
            $coordinates->setLongitude($longitude);
            $location->setAddress($address);
            $location->setCoordinates($coordinates);

            $size->setValue($area);
            $size->setMeasurement($measurement);

            $status = $this->entityManager->getRepository(PropertyStatus::class)->find('draft');

            $property->setAgent($agent);
            $property->setType($type);
            $property->setPrice($price);
            $property->setLocation($location);
            $property->setSize($size);
            $property->setDescription($description);
            $property->setStatus($status);

            $this->entityManager->persist($property);
            $this->entityManager->flush();
            return $property;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }

    public function updateProperty(
        Uuid $propertyId,
        ?string $propertyTypeId,
        ?float $priceAmount,
        ?string $priceCurrencyId,
        ?string $address,
        ?float $latitude,
        ?float $longitude,
        ?float $area,
        ?string $measurement,
        ?string $description,
    ) : Property
    {
        try {
            $property = $this->entityManager->getRepository(Property::class)->find($propertyId);
            if ($propertyTypeId) {
                $type = $this->entityManager->getRepository(PropertyType::class)->find($propertyTypeId);
                $property->setType($type);
            }
            if ($priceAmount) {
                $property->getPrice()->setAmount($priceAmount);
            }
            if($priceCurrencyId){
                $priceCurrency = $this->entityManager->getRepository(Currency::class)->find($priceCurrencyId);
                $property->getPrice()->setCurrency($priceCurrency);
            }
            if($address){
                $property->getLocation()->setAddress($address);
            }
            if($latitude){
                $property->getLocation()->getCoordinates()->setLatitude($latitude);
            }
            if($longitude){
                $property->getLocation()->getCoordinates()->setLongitude($longitude);
            }
            if($area){
                $property->getSize()->setValue($area);
            }
            if($measurement){
                $property->getSize()->setMeasurement($measurement);
            }
            if($description) {
                $property->setDescription($description);
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

    public function changePropertyStatus(string $propertyId, string $statusId): Property
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
