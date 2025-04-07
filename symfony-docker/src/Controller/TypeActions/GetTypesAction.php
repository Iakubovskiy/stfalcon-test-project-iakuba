<?php
declare(strict_types=1);


namespace App\Controller\TypeActions;

use App\Presenters\PropertyTypePresenter;
use App\Services\TypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class GetTypesAction extends AbstractController
{
    public function __construct(
        private readonly TypeService $typeService,
        private readonly PropertyTypePresenter $propertyTypePresenter
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
}
