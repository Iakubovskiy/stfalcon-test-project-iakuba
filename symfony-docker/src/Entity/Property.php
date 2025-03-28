<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PropertyRepository;
use App\Types\Price;
use App\Types\PropertyLocation;
use App\Types\PropertySize;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(["list", "details"])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["list", "details"])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(["list", "details"])]
    private ?Price $price = null;

    #[ORM\Column]
    #[Groups(["list", "details"])]
    private ?PropertySize $size = null;

    #[ORM\Column]
    #[Groups(["list", "details"])]
    private ?PropertyLocation $location = null;

    #[ORM\ManyToOne(targetEntity: Agent::class, inversedBy: 'properties')]
    private Agent $agent;

    #[ORM\ManyToOne(targetEntity: PropertyStatus::class, inversedBy: 'properties')]
    #[Groups(["list", "details"])]
    private PropertyStatus $status;

    #[ORM\ManyToOne(targetEntity: PropertyType::class, inversedBy: 'properties')]
    #[Groups(["list", "details"])]
    private PropertyType $type;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getSize(): ?PropertySize
    {
        return $this->size;
    }

    public function setSize(PropertySize $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getLocation(): ?PropertyLocation
    {
        return $this->location;
    }

    public function setLocation(PropertyLocation $location): static
    {
        $this->location = $location;
        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(Agent $agent): static
    {
        $this->agent = $agent;
        return $this;
    }

    public function getStatus(): PropertyStatus
    {
        return $this->status;
    }

    public function setStatus(PropertyStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getType(): PropertyType
    {
        return $this->type;
    }

    public function setType(PropertyType $type): static
    {
        $this->type = $type;
        return $this;
    }
}
