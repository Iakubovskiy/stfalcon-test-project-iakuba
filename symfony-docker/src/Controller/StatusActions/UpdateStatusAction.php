<?php
declare(strict_types=1);


namespace App\Controller\StatusActions;

use App\Presenters\PropertyStatusPresenter;
use App\Services\StatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Uid\Uuid;

class UpdateStatusAction extends AbstractController
{
    public function __construct(
        private readonly StatusService $statusService,
        private readonly PropertyStatusPresenter $propertyStatusPresenter,
    )
    {}

    #[Route('api/statuses/{id}', name: 'status_update', methods: ['PUT'])]
    #[OA\Put(
        description: "Modifies the name of an existing entity status.",
        summary: "Update an existing status",
        requestBody: new OA\RequestBody(
            description: "New status name",
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", description: "The updated status name", type: "string", example: "Pending Approval")
                ],
                type: "object"
            )
        ),
        tags: ["Statuses"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The identifier of the status to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'PENDING')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Status updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "PENDING"),
                        new OA\Property(property: "name", type: "string", example: "Pending Approval")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON payload or missing 'name' field"),
            new OA\Response(response: 404, description: "Not Found - Status with the specified ID not found"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function updateStatus(Request $request, Uuid $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if($data === []) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }
        $name = $data['name'];
        return new JsonResponse(
            $this->propertyStatusPresenter->present($this->statusService->update($id, $name)),
            Response::HTTP_OK,
            [],
        );
    }
}
