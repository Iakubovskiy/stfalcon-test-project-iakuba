<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\PropertyType;

final readonly class PropertyTypePresenter
{
    public function present(PropertyType $propertyType): array
    {
        return [
          'id' => $propertyType->getId(),
          'name' => $propertyType->getName(),
        ];
    }

    public function presentList(array $propertyTypes): array
    {
        return array_map(fn(PropertyType $propertyType) => $this->present($propertyType), $propertyTypes);
    }
}
