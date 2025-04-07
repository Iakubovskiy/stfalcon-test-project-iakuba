<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\PropertyCreateDto;
use App\DTO\PropertyUpdateDto;
use App\Entity\Coordinates;
use App\Entity\Price;
use App\Entity\Property;
use App\Entity\PropertyLocation;
use App\Entity\PropertySize;
use App\Repository\AgentRepository;
use App\Repository\PropertyRepository;
use App\Repository\PropertyStatusRepository;
use App\Repository\PropertyTypeRepository;
use App\Services\S3\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class PropertyService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private S3Service $s3Service,
        private PropertyStatusRepository $propertyStatusRepository,
        private PropertyTypeRepository $propertyTypeRepository,
        private PropertyRepository $propertyRepository,
        private AgentRepository $agentRepository,
        private FileService $fileService,
    )
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
            return $this->propertyRepository->find($propertyId);
        } catch (EntityNotFoundException $exception) {
            return null;
        }
    }

    public function getAllPropertiesForUsers(array $visibleStatuses,int $offset, int $limit):array
    {
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
    }

    public function getAgentProperty(Uuid $agentId): array
    {
        $agent = $this->agentRepository->find($agentId);
        return iterator_to_array($agent->getProperties());
    }

    public function createProperty(
        PropertyCreateDto $propertyCreateDto,
    ): Property
    {
        $property = new Property();
        $agent = $this->agentRepository->find($propertyCreateDto->agentId);
        $type = $this->propertyTypeRepository->find($propertyCreateDto->propertyTypeId);
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

        $status = $this->propertyStatusRepository->findOneBy(['name'=>'Draft']);

        $property->setAgent($agent);
        $property->setType($type);
        $property->setPrice($price);
        $property->setLocation($location);
        $property->setSize($size);
        $property->setDescription($propertyCreateDto->description);
        $property->setStatus($status);

        if($propertyCreateDto->images) {
            $photoUrls = array_map(
                fn ($file)=> $this->fileService->getFileById(UUid::fromString($file))->getUrl(),
                $propertyCreateDto->images,
            );
            $property->setPhotoUrls($photoUrls);
        }

        $this->entityManager->persist($property);
        $this->entityManager->flush();
        return $property;
    }

    public function updateProperty(Uuid $propertyId, PropertyUpdateDto $propertyUpdateDto) : Property
    {
        $property = $this->propertyRepository->find($propertyId);
        if ($propertyUpdateDto->propertyTypeId) {
            $type = $this->propertyTypeRepository->find($propertyUpdateDto->propertyTypeId);
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
    }

    public function deleteProperty(Uuid $propertyId): bool
    {
        $property = $this->propertyRepository->find($propertyId);
        $this->entityManager->remove($property);
        $this->entityManager->flush();
        return true;
    }

    public function changePropertyStatus(Uuid $propertyId, string $statusId): Property
    {
            $property = $this->propertyRepository->find($propertyId);
            $status = $this->propertyStatusRepository->find($statusId);
            $property->setStatus($status);
            $this->entityManager->persist($property);
            $this->entityManager->flush();
            return $property;
    }

    public function updatePropertyPhotos(Uuid $propertyId, array $images) : Property
    {
        $property = $this->propertyRepository->find($propertyId);
        if($images) {
            $photoUrls = $this->s3Service->saveFileArray($images);
            $property->setPhotoUrls($photoUrls);
        }
        $this->entityManager->persist($property);
        $this->entityManager->flush();
        return $property;
    }
}
