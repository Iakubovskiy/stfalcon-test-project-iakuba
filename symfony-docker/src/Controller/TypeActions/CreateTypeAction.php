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

class CreateTypeAction extends AbstractController
{
    public function __construct(
        private readonly TypeService $typeService,
        private readonly PropertyTypePresenter $propertyTypePresenter
    )
    {}

    #[Route('api/types', name: 'type_create', methods: ['POST'])]
    #[OA\Post(
        description: "Adds a new entity type definition.",
        summary: "Create a new type",
        requestBody: new OA\RequestBody(
            description: "Type data",
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
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
        $name = $data['name'];

        return new JsonResponse(
            $this->propertyTypePresenter->present($this->typeService->createType($name)),
            Response::HTTP_CREATED,
            [],
        );
    }
}
