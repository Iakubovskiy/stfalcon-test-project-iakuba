<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PropertyStatusRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PropertyStatusRepository::class)]
class PropertyStatus
{
    #[ORM\Id]
    #[ORM\Column]
    private ?string $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Property::class, mappedBy: 'status')]
    private Collection $properties;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getProperties(): Collection
    {
        return $this->properties;
    }

    public function setProperties(Collection $properties): static
    {
        $this->properties = $properties;
        return $this;
    }
}
