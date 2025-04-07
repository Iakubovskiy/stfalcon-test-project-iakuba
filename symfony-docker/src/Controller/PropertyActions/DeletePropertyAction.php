<?php
declare(strict_types=1);


namespace App\Controller\PropertyActions;

use OpenApi\Attributes as OA;
use App\Services\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class DeletePropertyAction extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
    )
    {}

    #[Route('api/properties/{id}', name: 'property_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: "Removes a property listing.",
        summary: "Delete a property",
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID to delete', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Property deleted successfully"
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid UUID format"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to delete this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function propertyDelete(Uuid $id):JsonResponse
    {
        $this->propertyService->deleteProperty($id);
        return new JsonResponse(Response::HTTP_NO_CONTENT);
    }
}
