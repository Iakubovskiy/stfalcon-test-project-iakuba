<?php
declare(strict_types=1);


namespace App\DTO;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class PropertyCreateDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 100)]
        public readonly string $propertyTypeId,

        #[Assert\NotBlank]
        #[Assert\Uuid]
        public readonly Uuid $agentId,

        #[Assert\NotBlank]
        public readonly float $priceAmount,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 4)]
        public readonly string $priceCurrencyId,

        #[Assert\NotBlank]
        public float $latitude,

        #[Assert\NotBlank]
        public float $longitude,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 100)]
        public readonly string $address,

        #[Assert\NotBlank]
        public readonly float $area,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 10)]
        public readonly string $measurement,

        #[Assert\NotBlank]
        #[Assert\Length(min: 1, max: 200)]
        public readonly string $description,
    ) {
    }
}
