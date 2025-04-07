<?php
declare(strict_types=1);


namespace App\DTO;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyUpdateDto
{
    public function __construct(
        #[Assert\Length(min: 1, max: 100)]
        public readonly ?Uuid $propertyTypeId = null,

        public readonly ?float $priceAmount = null,

        #[Assert\Length(min: 1, max: 100)]
        public readonly ?Uuid $priceCurrencyId = null,

        public ?float $latitude = null,

        public ?float $longitude = null,

        #[Assert\Length(min: 1, max: 100)]
        public readonly ?string $address = null,

        public readonly ?float $area = null,

        #[Assert\Length(min: 1, max: 10)]
        public readonly ?string $measurement = null,

        #[Assert\Length(min: 1, max: 200)]
        public readonly ?string $description = null,
    ) {
    }
}
