<?php
declare(strict_types=1);

namespace App\Types;

use JMS\Serializer\Annotation\Groups;

class PropertySize{
    #[Groups(["list", "details"])]
    private float $value;
    #[Groups(["list", "details"])]
    private string $measurement;

    public function getValue(): float{
        return $this->value;
    }

    public function setValue(float $value): static{
        $this->value = $value;
        return $this;
    }

    public function getMeasurement(): string{
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): static{
        $this->measurement = $measurement;
        return $this;
    }
}
