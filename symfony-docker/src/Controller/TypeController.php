<?php
declare(strict_types=1);


namespace App\Controller;

use App\Presenters\PropertyTypePresenter;
use App\Services\TypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class TypeController extends AbstractController
{
    public function __construct(
        private TypeService $typeService,
        private PropertyTypePresenter $propertyTypePresenter
    )
    {}

    #[Route('api/types', name: 'types', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves all available entity types.",
        summary: "Get list of types",
        tags: ["Types"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of types",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: "id", description: "Type identifier", type: "string", example: "ORDER_TYPE"),
                            new OA\Property(property: "name", description: "Type name", type: "string", example: "Order Type")
                        ],
                        type: "object"
                    )
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function getTypes(): JsonResponse
    {
        return new JsonResponse(
            $this->propertyTypePresenter->presentList($this->typeService->getTypes()),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/type/create', name: 'type_create', methods: ['POST'])]
    #[OA\Post(
        description: "Adds a new entity type definition.",
        summary: "Create a new type",
        requestBody: new OA\RequestBody(
            description: "Type data",
            required: true,
            content: new OA\JsonContent(
                required: ["id", "name"],
                properties: [
                    new OA\Property(property: "id", description: "Unique type identifier", type: "string", example: "PRODUCT_TYPE"),
                    new OA\Property(property: "name", description: "Type name", type: "string", example: "Product Type")
                ],
                type: "object"
            )
        ),
        tags: ["Types"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Type created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "id", type: "string", example: "PRODUCT_TYPE"),
                        new OA\Property(property: "name", type: "string", example: "Product Type")
                    ],
                    type: "object"
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid JSON payload or missing fields"),
            new OA\Response(response: 409, description: "Conflict - Type with this ID already exists"),
            new OA\Response(response: 401, description: "Unauthorized - JWT token missing or invalid"),
            new OA\Response(response: 403, description: "Forbidden - Insufficient permissions")
        ]
    )]
    public function createType(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if($data === []) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }
        $id = $data['id'];
        $name = $data['name'];

        return new JsonResponse(
            $this->propertyTypePresenter->present($this->typeService->createType($id, $name)),
            Response::HTTP_CREATED,
            [],
        );
    }

    #[Route('api/type/update/{id}', name: 'type_update', methods: ['PUT'])]
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

    #[Route('api/type/delete/{id}', name: 'type_delete', methods: ['DELETE'])]
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
