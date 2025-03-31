<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    public function __construct(){
        $this->roles = ["ROLE_CUSTOMER"];
        $this->favoriteProperties = new ArrayCollection();
    }

    #[ORM\ManyToMany(targetEntity: Property::class)]
    protected Collection $favoriteProperties;

    public function getFavoriteProperties(): array
    {
        return $this->favoriteProperties->toArray();
    }

    public function setFavoriteProperties(array $favoriteProperties): self
    {
        $this->favoriteProperties = new ArrayCollection();
        foreach ($favoriteProperties as $favoriteProperty) {
            $this->addFavoriteProperty($favoriteProperty);
        }
        return $this;
    }

    public function addFavoriteProperty(Property $property): self
    {
        if (!$this->favoriteProperties->contains($property)) {
            $this->favoriteProperties->add($property);
        }
        return $this;
    }
    public function removeFavoriteProperty(Property $property): self
    {
        $this->favoriteProperties->removeElement($property);
        return $this;
    }
}
