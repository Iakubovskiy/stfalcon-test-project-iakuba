<?php
declare(strict_types=1);


namespace App\Controller\TypeActions;

use App\Services\TypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class DeleteTypeAction extends AbstractController
{
    public function __construct(
        private readonly TypeService $typeService,
    )
    {}

    #[Route('api/types/{id}', name: 'type_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: "Removes an entity type definition.",
        summary: "Delete a type",
        tags: ["Types"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The identifier of the type to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'TEMP_TYPE')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Type deleted successfully (No Content)"
            ),
            new OA\Response(response: 404, description: "Not Found - Type with the specified ID not found"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function deleteType(string $id): JsonResponse
    {
        $this->typeService->deleteType($id);
        return new JsonResponse(Response::HTTP_NO_CONTENT);
    }
}
