<?php
declare(strict_types=1);


namespace App\DTO;

class ChangeStatusDTO
{
    public function __construct(public string $statusId){}
}
