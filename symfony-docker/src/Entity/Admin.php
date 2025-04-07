<?php
declare(strict_types=1);

namespace App\Entity;

use App\Enum\Role;
use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    public function __construct(){
        $this->roles = [Role::ADMIN->value];
    }
}
