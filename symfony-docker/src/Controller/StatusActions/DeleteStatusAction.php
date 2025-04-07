<?php
declare(strict_types=1);


namespace App\Controller\StatusActions;

use App\Presenters\PropertyStatusPresenter;
use App\Services\StatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

class DeleteStatusAction extends AbstractController
{
    public function __construct(
        private readonly StatusService $statusService,
    )
    {}

    #[Route('api/statuses/{id}', name: 'status_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: "Removes an entity status definition.",
        summary: "Delete a status",
        tags: ["Statuses"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The identifier of the status to delete',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'ARCHIVED')
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Status deleted successfully (No Content)"
            ),
            new OA\Response(response: 404, description: "Not Found - Status with the specified ID not found"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function deleteStatus(Uuid $id): JsonResponse
    {
        $this->statusService->delete($id);
        return new JsonResponse(Response::HTTP_NO_CONTENT);
    }
}
