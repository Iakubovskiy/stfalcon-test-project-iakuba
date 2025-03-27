<?php
declare(strict_types=1);

namespace App\Types;

use JMS\Serializer\Annotation\Groups;

class Coordinates
{
    #[Groups(["list", "details"])]
    private float $latitude;
    #[Groups(["list", "details"])]
    private float $longitude;

    public function getLatitude(): float{
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }
}
