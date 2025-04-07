<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class PropertySize{
    #[ORM\Column(type:'float')]
    private float $value;
    #[ORM\Column(type:'string')]
    private string $measurement;

    public function getValue(): float{
        return $this->value;
    }

    public function setValue(float $value): self{
        $this->value = $value;
        return $this;
    }

    public function getMeasurement(): string{
        return $this->measurement;
    }

    public function setMeasurement(string $measurement): self{
        $this->measurement = $measurement;
        return $this;
    }
}
