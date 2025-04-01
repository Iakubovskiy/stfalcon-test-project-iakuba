<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PropertyRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]
class Property
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Embedded(class: Price::class)]
    private ?Price $price = null;

    #[ORM\Embedded(class: PropertySize::class)]
    private ?PropertySize $size = null;

    #[ORM\Embedded(class: PropertyLocation::class)]
    private ?PropertyLocation $location = null;

    #[ORM\ManyToOne(targetEntity: Agent::class, inversedBy: 'properties')]
    private Agent $agent;

    #[ORM\ManyToOne(targetEntity: PropertyStatus::class, inversedBy: 'properties')]
    private PropertyStatus $status;

    #[ORM\ManyToOne(targetEntity: PropertyType::class, inversedBy: 'properties')]
    private PropertyType $type;

    #[ORM\Column]
    private ?array $photoUrls = [];

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

    public function getPhotoUrls(): ?array
    {
        return $this->photoUrls;
    }

    public function setPhotoUrls(array $photoUrls): self
    {
        $this->photoUrls = $photoUrls;
        return $this;
    }

    public function addPhotoUrl(string $photoUrl): self
    {
        $this->photoUrls[] = $photoUrl;
        return $this;
    }

    public function removePhotoUrl(string $photoUrl): self
    {
        $index = array_search($photoUrl, $this->photoUrls);
        unset($this->photoUrls[$index]);
        return $this;
    }
}
