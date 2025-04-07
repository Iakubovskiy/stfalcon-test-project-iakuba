<?php
declare(strict_types=1);


namespace App\Presenters;

use App\Entity\File;

class FilePresenter
{
    public function present(File $file): array
    {
        return [
            'id' => $file->getId(),
            'url' => $file->getUrl(),
        ];
    }

    public function presentArray(array $files): array
    {
        return array_map(fn ($file) => $this->present($file), $files);
    }
}
