<?php
declare(strict_types=1);

namespace App\Types;

use JMS\Serializer\Annotation\Groups;

class PropertyLocation
{
    #[Groups(["list", "details"])]
    private string $address;
    #[Groups(["list", "details"])]
    private Coordinates $coordinates;

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function setCoordinates(Coordinates $coordinates): static
    {
        $this->coordinates = $coordinates;
        return $this;
    }
}
