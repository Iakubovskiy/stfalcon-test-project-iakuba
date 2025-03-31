<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\PropertySize;

class SizePresenter
{
    public function present(PropertySize $size): array
    {
        return [
            'value' => $size->getValue(),
            'measurement' => $size->getMeasurement(),
        ];
    }
}
