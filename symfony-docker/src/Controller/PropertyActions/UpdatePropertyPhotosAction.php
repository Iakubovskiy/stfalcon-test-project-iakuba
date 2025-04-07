<?php
declare(strict_types=1);


namespace App\Controller\PropertyActions;

use App\Presenters\PropertyPresenter;
use App\Services\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class UpdatePropertyPhotosAction extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly PropertyPresenter $propertyPresenter,
    )
    {}
    #[Route('api/properties/{id}/photos', name: 'property_update_photos', methods: ['POST'])]
    public function propertyUpdatePhotos(
        Request $request,
        Uuid $id,
    ):JsonResponse
    {
        $images = $request->files->get('images');
        return new JsonResponse(
            $this->propertyPresenter->present($this->propertyService->updatePropertyPhotos($id, $images)),
            Response::HTTP_OK,
            [],
        );
    }
}
