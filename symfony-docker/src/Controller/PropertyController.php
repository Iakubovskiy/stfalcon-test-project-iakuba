<?php
declare(strict_types=1);


namespace App\Controller;

use App\DTO\ChangeStatusDTO;
use App\DTO\PaginatedListDTO;
use App\DTO\PropertyCreateDto;
use App\DTO\PropertyForUserDTO;
use App\DTO\PropertyUpdateDto;
use App\Presenters\PropertyPresenter;
use App\Services\PropertyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

class PropertyController extends AbstractController
{
    public function __construct(
        private readonly PropertyService $propertyService,
        private readonly PropertyPresenter $propertyPresenter,
    )
    {}

    #[Route('api/admin/property', name: 'admin_property', methods: ['POST'])]
    #[OA\Post(
        description: "Retrieves a paginated list of all properties. Intended for administrators.",
        summary: "Get all properties (Admin, Paginated)",
        requestBody: new OA\RequestBody(
            description: "metadata for paginated list",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PaginatedListDTO::class))
        ),
        tags: ["Property"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Paginated list of properties",
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
                                    new OA\Property(property: 'price', properties: [
                                        new OA\Property(property: 'amount', type: 'integer', example: 150000),
                                        new OA\Property(property: 'currency', properties: [
                                            new OA\Property(property: 'id', type: 'string', example: 'usd'),
                                            new OA\Property(property: 'name', type: 'string', example: 'USD')
                                        ], type: 'object')
                                    ], type: 'object'),
                                    new OA\Property(property: 'location', properties: [
                                        new OA\Property(property: 'address', type: 'string', example: 'Kyiv, Ukraine'),
                                        new OA\Property(property: 'coordinates', properties: [
                                            new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 50.4501),
                                            new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 30.5234)
                                        ], type: 'object')
                                    ], type: 'object'),
                                    new OA\Property(property: 'size', properties: [
                                        new OA\Property(property: 'value', type: 'integer', example: 70),
                                        new OA\Property(property: 'measurement', type: 'string', example: 'm²')
                                    ], type: 'object'),
                                    new OA\Property(property: 'description', type: 'string', example: 'A cozy 2-bedroom apartment...'),
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
            new OA\Response(response: 400, description: "Bad Request - Invalid pagination parameters"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - User is not an administrator")
        ]
    )]
    public function adminProperty(
        #[MapRequestPayload] PaginatedListDTO $listDTO,
    ): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->presentPaginatedProperty(
                $this->propertyService->getAllProperties($listDTO->offset, $listDTO->limit)
            ),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/property/agent/{id}', name: 'agent_property', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves a list of properties associated with the specified agent ID.",
        summary: "Get properties for a specific agent",
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Agent UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of properties for the agent",
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'),
                            new OA\Property(property: 'type', properties: [
                                new OA\Property(property: 'id', type: 'string', example: 'land'),
                                new OA\Property(property: 'name', type: 'string', example: 'Land Properties')
                            ], type: 'object'),
                            new OA\Property(property: 'price', properties: [
                                new OA\Property(property: 'amount', type: 'integer', example: 150000),
                                new OA\Property(property: 'currency', properties: [
                                    new OA\Property(property: 'id', type: 'string', example: 'usd'),
                                    new OA\Property(property: 'name', type: 'string', example: 'USD')
                                ], type: 'object')
                            ], type: 'object'),
                            new OA\Property(property: 'location', properties: [
                                new OA\Property(property: 'address', type: 'string', example: 'Kyiv, Ukraine'),
                                new OA\Property(property: 'coordinates', properties: [
                                    new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 50.4501),
                                    new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 30.5234)
                                ], type: 'object')
                            ], type: 'object'),
                            new OA\Property(property: 'size', properties: [
                                new OA\Property(property: 'value', type: 'integer', example: 120),
                                new OA\Property(property: 'measurement', type: 'string', example: 'm²')
                            ], type: 'object'),
                            new OA\Property(property: 'description', type: 'string', example: '120 m² of prime land in Kyiv...'),
                            new OA\Property(property: 'status', properties: [
                                new OA\Property(property: 'id', type: 'string', example: 'draft'),
                                new OA\Property(property: 'name', type: 'string', example: 'Draft')
                            ], type: 'object')
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid Agent UUID format"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to view this agent's properties"),
            new OA\Response(response: 404, description: "Not Found - Agent with the specified ID not found")
        ]
    )]
    public function agentProperty(Uuid $id): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->presentProperties($this->propertyService->getAgentProperty($id)),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/property/{id}', name: 'property_property', methods: ['GET'])]
    #[OA\Get(
        description: "Retrieves the details of a single property.",
        summary: "Get a specific property by ID",
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property details",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'),
                        new OA\Property(property: 'type', properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'land'),
                            new OA\Property(property: 'name', type: 'string', example: 'Land Properties')
                        ], type: 'object'),
                        new OA\Property(property: 'price', properties: [
                            new OA\Property(property: 'amount', type: 'integer', example: 150000),
                            new OA\Property(property: 'currency', properties: [
                                new OA\Property(property: 'id', type: 'string', example: 'usd'),
                                new OA\Property(property: 'name', type: 'string', example: 'USD')
                            ], type: 'object')
                        ], type: 'object'),
                        new OA\Property(property: 'location', properties: [
                            new OA\Property(property: 'address', type: 'string', example: 'Kyiv, Ukraine'),
                            new OA\Property(property: 'coordinates', properties: [
                                new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 50.4501),
                                new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 30.5234)
                            ], type: 'object')
                        ], type: 'object'),
                        new OA\Property(property: 'size', properties: [
                            new OA\Property(property: 'value', type: 'integer', example: 120),
                            new OA\Property(property: 'measurement', type: 'string', example: 'm²')
                        ], type: 'object'),
                        new OA\Property(property: 'description', type: 'string', example: '120 m² of prime land in Kyiv...'),
                        new OA\Property(property: 'status', properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'draft'),
                            new OA\Property(property: 'name', type: 'string', example: 'Draft')
                        ], type: 'object')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid Property UUID format"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to view this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function property(Uuid $id): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->presentPropertyDetails($this->propertyService->getProperty($id)),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/property-for-user', name: 'property_for_user', methods: ['POST'])]
    #[OA\Post(
        description: "Retrieves a paginated list of properties based on user visibility rules and status filters passed in the request body. Uses query parameters for pagination.",
        summary: "Get properties visible to the user (Filtered, Paginated)",
        requestBody: new OA\RequestBody(
            description: "Filters for properties (e.g., visible statuses)",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'filters',
                        ref: new Model(type: PropertyForUserDTO::class)
                    ),
                    new OA\Property(
                        property: 'pagination',
                        ref: new Model(type: PaginatedListDTO::class)
                    ),
                ],
                type: 'object'
            )
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
                                    new OA\Property(property: 'price', properties: [
                                        new OA\Property(property: 'amount', type: 'integer', example: 150000),
                                        new OA\Property(property: 'currency', properties: [
                                            new OA\Property(property: 'id', type: 'string', example: 'usd'),
                                            new OA\Property(property: 'name', type: 'string', example: 'USD')
                                        ], type: 'object')
                                    ], type: 'object'),
                                    new OA\Property(property: 'location', properties: [
                                        new OA\Property(property: 'address', type: 'string', example: 'Kyiv, Ukraine'),
                                        new OA\Property(property: 'coordinates', properties: [
                                            new OA\Property(property: 'latitude', type: 'number', format: 'float', example: 50.4501),
                                            new OA\Property(property: 'longitude', type: 'number', format: 'float', example: 30.5234)
                                        ], type: 'object')
                                    ], type: 'object'),
                                    new OA\Property(property: 'size', properties: [
                                        new OA\Property(property: 'value', type: 'integer', example: 70),
                                        new OA\Property(property: 'measurement', type: 'string', example: 'm²')
                                    ], type: 'object'),
                                    new OA\Property(property: 'description', type: 'string', example: 'A cozy 2-bedroom apartment...'),
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
                                new OA\Property(property: 'total', type: 'integer', example: 50) // Приклад
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
    public function propertyForUser(
        #[MapRequestPayload]PropertyForUserDTO $forUserDTO,
        #[MapRequestPayload]PaginatedListDTO $paginatedListDTO,
    ): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->presentPaginatedProperty(
                $this->propertyService->getAllPropertiesForUsers(
                    $forUserDTO->visibleStatuses,
                    $paginatedListDTO->offset,
                    $paginatedListDTO->limit
                ),
            ),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/property/create', name: 'property_create', methods: ['POST'])]
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

    #[Route('api/property/update/{id}', name: 'property_update', methods: ['PUT'])]
    #[OA\Put(
        description: "Modifies the details of an existing property.",
        summary: "Update an existing property",
        requestBody: new OA\RequestBody(
            description: "Updated data for the property",
            required: true,
            content: new OA\JsonContent(ref: new Model(type: PropertyUpdateDto::class))
        ),
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID to update', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'), // ID оновленого
                        new OA\Property(property: 'type', properties: [ /* ... */ ], type: 'object'),
                        new OA\Property(property: 'price', properties: [ /* ... */ ], type: 'object'),
                        new OA\Property(property: 'status', properties: [ /* ... */ ], type: 'object')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid data provided (validation failed) or invalid UUID"), // Або 422
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to update this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function propertyUpdate(
        #[MapRequestPayload] PropertyUpdateDto $propertyUpdateDto,
        Uuid $id,
    ):JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->present($this->propertyService->updateProperty($id, $propertyUpdateDto)),
            Response::HTTP_OK,
            [],
        );
    }

    #[Route('api/property/delete/{id}', name: 'property_delete', methods: ['DELETE'])]
    #[OA\Delete(
        description: "Removes a property listing.",
        summary: "Delete a property",
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID to delete', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Property deleted successfully"
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid UUID format"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to delete this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function propertyDelete(Uuid $id):JsonResponse
    {
        $this->propertyService->deleteProperty($id);
        return new JsonResponse(Response::HTTP_NO_CONTENT);
    }

    #[Route('api/property/change-status/{id}', name: 'property_change_status', methods: ['PATCH'])]
    #[OA\Patch(
        description: "Updates the status of a specific property using status ID.",
        summary: "Change the status of a property",
        requestBody: new OA\RequestBody(
            description: "Updated data for the property",
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "statusId", type: "string", example: "draft")
                ],
                type: "object"
            )
        ),
        tags: ["Property"],
        parameters: [
            new OA\Parameter(name: 'id', description: 'Property UUID', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Property status changed successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '550e8400-e29b-41d4-a716-446655440003'),
                        new OA\Property(property: 'status', properties: [
                            new OA\Property(property: 'id', type: 'string', example: 'available'),
                            new OA\Property(property: 'name', type: 'string', example: 'Available')
                        ], type: 'object')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 400, description: "Bad Request - Invalid UUID format, invalid status transition, or status ID does not exist"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 403, description: "Forbidden - Not allowed to change status for this property"),
            new OA\Response(response: 404, description: "Not Found - Property with the specified ID not found")
        ]
    )]
    public function propertyChangeStatus(Uuid $id, #[MapRequestPayload] ChangeStatusDTO $changeStatusDTO): JsonResponse
    {
        return new JsonResponse(
            $this->propertyPresenter->present($this->propertyService->changePropertyStatus($id, $changeStatusDTO->statusId)),
            Response::HTTP_OK,
            []
        );
    }
}
