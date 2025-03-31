<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\PropertyLocation;

class LocationPresenter
{
    public function present(PropertyLocation $location): array
    {
        return [
            'address' => $location->getAddress(),
            'coordinates' => [
                'latitude' => $location->getCoordinates()->getLatitude(),
                'longitude' => $location->getCoordinates()->getLongitude(),
            ],
        ];
    }
}
