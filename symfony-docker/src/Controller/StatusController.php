<?php
declare(strict_types=1);


namespace App\Controller;

use App\Presenters\PropertyStatusPresenter;
use App\Services\StatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class StatusController extends AbstractController
{
    public function __construct(
        private readonly StatusService $statusService,
        private readonly PropertyStatusPresenter $propertyStatusPresenter,
    )
    {}

    #[Route('api/statuses', name: 'status', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves all available entity statuses.",
        summary: "Get list of statuses",
        tags: ["Statuses"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of statuses",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", description: "Status identifier", type: "string", example: "PENDING"),
                            new OA\Property(property: "name", description: "Status name", type: "string", example: "Pending")
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function getStatuses(): JsonResponse
    {
        return new JsonResponse(
            $this->propertyStatusPresenter->presentArray($this->statusService->getAllStatuses()),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/status/create', name: 'status_create', methods: ['POST'])]
    #[OA\Post(
        description: "Adds a new entity status definition.",
        summary: "Create a new status",
        requestBody: new OA\RequestBody(
            description: "Status data",
            required: true,
            content: new OA\JsonContent(
                required: ["id", "name"],
                properties: [
                    new OA\Property(property: "id", description: "Unique status identifier", type: "string", example: "COMPLETED"),
                    new OA\Property(property: "name", description: "Status name", type: "string", example: "Completed")
                ],
                type: "object"
            )
        ),
        tags: ["Statuses"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Status created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "COMPLETED"),
                        new OA\Property(property: "name", type: "string", example: "Completed")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON payload or missing fields"),
            new OA\Response(response: 409, description: "Conflict - Status with this ID already exists"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function createStatus(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if($data === []) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }
        $id = $data['id'];
        $name = $data['name'];

        return new JsonResponse(
            $this->propertyStatusPresenter->present($this->statusService->create($id, $name)),
            Response::HTTP_CREATED,
            [],
        );
    }

    #[Route('api/status/update/{id}', name: 'status_update', methods: ['PUT'])]
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
    public function updateStatus(Request $request, string $id): JsonResponse
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

    #[Route('api/status/delete/{id}', name: 'status_delete', methods: ['DELETE'])]
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
    public function deleteStatus(string $id): JsonResponse
    {
        return new JsonResponse(Response::HTTP_NO_CONTENT);
    }
}
