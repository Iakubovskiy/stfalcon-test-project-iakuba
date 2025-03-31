<?php
declare(strict_types=1);


namespace App\DTO;

class PropertyForUserDTO
{
    public function __construct(
        public array $visibleStatuses,
    ){}
}
