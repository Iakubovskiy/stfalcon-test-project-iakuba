<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    public function __construct(){
        $this->roles = ["ROLE_CUSTOMER"];
    }
}
