<?php
declare(strict_types=1);


namespace App\Controller\PropertyActions;

use App\DTO\PropertyCreateDto;
use App\Presenters\PropertyPresenter;
use App\Services\PropertyService;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class CreatePropertyAction extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly PropertyPresenter $propertyPresenter,
    )
    {}

    #[Route('api/properties', name: 'property_create', methods: ['POST'])]
    #[OA\Post(
        description: "Retrieves a paginated list of properties based on user visibility rules and status filters passed in the request body.",
        summary: "Get properties visible to the user (Filtered, Paginated)",
        requestBody: new OA\RequestBody(
            description: "Data for the new user",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PropertyCreateDto::class))
        ),
        tags: ["Property"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Paginated list of properties for the user",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'result',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440002'),
                                    new OA\Property(property: 'type', properties: [
                                        new OA\Property(property: 'id', type: 'string', example: 'residential'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Residential Properties')
                                    ], type: 'object'),
                                    new OA\Property(property: 'status', properties: [
                                        new OA\Property(property: 'id', type: 'string', example: 'available'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Available')
                                    ], type: 'object')
                                ],
                                type: 'object'
                            )
                        ),
                        new OA\Property(
                            property: 'metadata',
                            properties: [
                                new OA\Property(property: 'limit', type: 'integer', example: 10),
                                new OA\Property(property: 'offset', type: 'integer', example: 0),
                                new OA\Property(property: 'total', type: 'integer', example: 50)
                            ],
                            type: 'object'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid request body or pagination parameters"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden")
        ]
    )]
    public function propertyCreate(
        #[MapRequestPayload] PropertyCreateDto $propertyCreateDto,
    ): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->present($this->propertyService->createProperty($propertyCreateDto)),
            Response::HTTP_CREATED,
            [],
        );
    }
}
