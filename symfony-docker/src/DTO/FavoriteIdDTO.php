<?php
declare(strict_types=1);


namespace App\DTO;

use Symfony\Component\Uid\Uuid;

class FavoriteIdDTO
{
    public function __construct(
        public Uuid $propertyId,
    )
    {}
}
