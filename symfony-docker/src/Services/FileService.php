<?php
declare(strict_types=1);


namespace App\Services;

use App\Entity\File;
use App\Repository\FileRepository;
use App\Services\S3\S3Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class FileService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private S3Service $s3Service,
        private FileRepository $fileRepository,
    )
    {

    }
    public function saveFilesToDb(array $images) : array
    {
        $photoUrls = $this->s3Service->saveFileArray($images);
        $files = [];
        foreach ($photoUrls as $photoUrl) {
            $photo = new File();
            $photo->setUrl($photoUrl);
            $this->entityManager->persist($photo);
            $files[] = $photo;
        }
        $this->entityManager->flush();
        return $files;
    }

    public function getFileById(Uuid $id) : File
    {
        return $this->fileRepository->find($id);
    }
}
