<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\PropertyStatus;

final readonly class PropertyStatusPresenter
{
    public function present(PropertyStatus $propertyStatus): array
    {
        return [
          'id' => $propertyStatus->getId(),
          'name' => $propertyStatus->getName(),
        ];
    }

    public function presentArray(array $propertyStatus): array
    {
        return array_map(fn(PropertyStatus $propertyStatus) => $this->present($propertyStatus), $propertyStatus);
    }
}
