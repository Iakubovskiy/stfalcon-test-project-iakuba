<?php
declare(strict_types=1);


namespace App\Controller\TypeActions;

use App\Presenters\PropertyTypePresenter;
use App\Services\TypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class UpdateTypeAction extends AbstractController
{
    public function __construct(
        private readonly TypeService $typeService,
        private readonly PropertyTypePresenter $propertyTypePresenter
    )
    {}

    #[Route('api/types/{id}', name: 'type_update', methods: ['PUT'])]
    #[OA\Put(
        description: "Modifies the name of an existing entity type.",
        summary: "Update an existing type",
        requestBody: new OA\RequestBody(
            description: "New type name",
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", description: "The updated type name", type: "string", example: "Updated Order Type")
                ],
                type: "object"
            )
        ),
        tags: ["Types"],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'The identifier of the type to update',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: 'ORDER_TYPE')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Type updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "ORDER_TYPE"),
                        new OA\Property(property: "name", type: "string", example: "Updated Order Type")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON payload or missing 'name' field"),
            new OA\Response(response: 404, description: "Not Found - Type with the specified ID not found"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function updateType(Request $request, string $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if($data === []) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }
        $name = $data['name'];
        return new JsonResponse(
            $this->propertyTypePresenter->present($this->typeService->updateType($id, $name)),
            Response::HTTP_OK,
            [],
        );
    }
}
