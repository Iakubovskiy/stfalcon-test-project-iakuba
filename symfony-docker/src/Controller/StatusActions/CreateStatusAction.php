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

class CreateStatusAction extends AbstractController
{
    public function __construct(
        private readonly StatusService $statusService,
        private readonly PropertyStatusPresenter $propertyStatusPresenter,
    )
    {}

    #[Route('api/statuses', name: 'status_create', methods: ['POST'])]
    #[OA\Post(
        description: "Adds a new entity status definition.",
        summary: "Create a new status",
        requestBody: new OA\RequestBody(
            description: "Status data",
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
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
        $name = $data['name'];

        return new JsonResponse(
            $this->propertyStatusPresenter->present($this->statusService->create($name)),
            Response::HTTP_CREATED,
            [],
        );
    }
}
