<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\Property;

final readonly class PropertyPresenter
{
    public function __construct(
        private readonly PropertyTypePresenter $propertyTypePresenter,
        private readonly PricePresenter $pricePresenter,
        private readonly LocationPresenter $locationPresenter,
        private readonly SizePresenter $sizePresenter,
        private readonly PropertyStatusPresenter $statusPresenter,
        private readonly UserPresenter $userPresenter,
    )
    {}

    public function present(Property $property): array
    {
        return [
            'id' => $property->getId(),
            'type' => $this->propertyTypePresenter->present($property->getType()),
            'price' => $this->pricePresenter->present($property->getPrice()),
            'location' => $this->locationPresenter->present($property->getLocation()),
            'size' => $this->sizePresenter->present($property->getSize()),
            'description' => $property->getDescription(),
            'status' => $this->statusPresenter->present($property->getStatus()),
            'images' => $property->getPhotoUrls(),
        ];
    }

    public function presentPaginatedProperty(array $data): array
    {
        return [
            'result' => array_map(fn (Property $property) => $this->present($property), $data['result']),
            'metadata' => [
                'limit' => $data['limit'],
                'offset' => $data['offset'],
                'total' => $data['total'],
            ],
        ];
    }

    public function presentProperties(array $data): array
    {
        return [
            'result' => array_map(fn (Property $property) => $this->present($property), $data),
        ];
    }

    public function presentPropertyDetails(Property $property): array
    {
        $result = $this->present($property);
        $result['agent'] = $this->userPresenter->present($property->getAgent());
        return $result;
    }
}
